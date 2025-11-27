<?php

namespace App\Services;

use App\Models\Complaint_status_log;
use App\Notifications\ComplaintMoreInfoRequested;
use App\Notifications\ComplaintNoteAdded;
use App\Notifications\ComplaintStatusUpdated;
use App\Repositories\Contracts\ComplaintRepositoryInterface;
use App\Repositories\Contracts\ComplaintStatusRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class ComplaintService
{
    public function __construct(
        private ComplaintRepositoryInterface $repo,
        private ComplaintStatusRepositoryInterface $statusRepo,
    ) {}

    public function submit(array $data, array $files = []): array
    {
        $data['user_id'] = auth()->id();

        $complaint = $this->repo->create($data);

        Complaint_status_log::create([
            'complaint_id' =>$complaint->id,
            'new_status' => 'pending',
            'note' => 'Complaint created',
        ]);

        if (!empty($files)) {
            $this->repo->addFiles($complaint, $files);
        }
        return [
            'status' => true,
            'message' => 'Complaint submitted successfully',
            'tracking_number' => $complaint->tracking_number,
            'complaint_id' => $complaint->id
        ];
    }

    public function getDepartmentComplaints()
    {
        $departmentId = auth()->user()->department_id;

        return $this->repo->getByDepartment($departmentId);
    }


    public function updateStatus(int $complaintId, string $newStatus, ?string $note = null)
    {
        Cache::lock('complaint_lock_' . $complaintId, 10)->block(5, function () use ($complaintId, $newStatus, $note) {
            $complaint = $this->repo->find($complaintId);

            if (!$complaint) {
                throw new \Exception("الشكوى غير موجودة");
            }

            $oldStatus = $complaint->status;

            $this->repo->update($complaint->id, ['status' => $newStatus]);

            $complaint->refresh();
            $this->statusRepo->createForComplaint(
                $complaint,
                $newStatus,
                $note ?? 'تم تحديث الحالة',
                $oldStatus,
            );

            if ($complaint->user) {
                $complaint->user->notify(new ComplaintStatusUpdated($complaint, $newStatus));
            }
        });
    }

    public function addNote(int $complaintId, string $note) {
        $complaint = $this->repo->find($complaintId);
        $log = $this->statusRepo->createForComplaint(
            $complaint,
            $complaint->status,
            $note
        );

        if($complaint->user) {
            $complaint->user->notify(
                new ComplaintNoteAdded($complaint,$note)
            );
        }

        return $log;

    }

    public function requestMoreInfo(int $complaintId, string $message)
    {
        $complaint = $this->repo->find($complaintId);
        $this->statusRepo->createForComplaint(
            $complaint,
            $complaint->status,
            $message

        );
        if ($complaint->user) {
            $complaint->user->notify(
                new ComplaintMoreInfoRequested($complaint, $message)
            );
        }
        return true;
    }


}
