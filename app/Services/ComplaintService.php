<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\Complaint_status_log;
use App\Notifications\ComplaintMoreInfoRequested;
use App\Notifications\ComplaintNoteAdded;
use App\Notifications\ComplaintStatusUpdated;
use App\Repositories\Contracts\ComplaintRepositoryInterface;
use App\Repositories\Contracts\ComplaintStatusRepositoryInterface;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use App\Traits\Auditable;

class ComplaintService
{
    use Auditable;
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




    public function updateComplaint(int $complaintId, array $data, array $files = []): array
    {
        return DB::transaction(function () use ($complaintId, $data, $files) {

            $complaint = $this->repo->find($complaintId);

            if ($complaint->user_id !== auth()->id()) {
                throw new \Exception('unauthorised');
            }

            $complaint = $this->repo->updateComplaint($data, $complaint);

            if (!empty($files)) {
                $this->repo->addFiles($complaint, $files);
            }

            $this->statusRepo->createForComplaint(
                $complaint,
                $complaint->status,
                'complaint updated'
            );

            return [
                'status' => true,
                'message' => 'complaint updated successfully',
                'complaint_id' => $complaint->id
            ];
        });
    }


    public function getDepartmentComplaints()
    {
        $departmentId = auth()->user()->department_id;

        return Cache::remember("complaints_department_{$departmentId}", 60, function () use ($departmentId) {
            return $this->repo->getByDepartment($departmentId);
        });
    }


    public function updateStatus(int $complaintId, string $status, ?string $note = null)
    {
        Cache::lock('complaint_lock_' . $complaintId, 10)->block(5, function () use ($complaintId, $status, $note) {
            $complaint = $this->repo->find($complaintId);
            if (!$complaint) {
                throw new \Exception("the complaint not found");
            }

            $allowedTransitions = [
                'pending'    => ['processing', 'rejected'],
                'processing' => ['done', 'rejected'],
                'done'       => [],
                'rejected'   => [],
            ];

            $current = $complaint->status;

            if (!isset($allowedTransitions[$current])) {
                throw new \Exception('Invalid current status');
            }

            if (!in_array($status, $allowedTransitions[$current], true)) {
                throw new \Exception("Cannot change status from {$current} to {$status}");
            }

            if (in_array($status, ['done', 'rejected'], true)) {
                $complaint->handled_by = auth()->id();
            }


            $this->repo->updateStatus($complaint, $status);

            Cache::forget("complaints_department_{$complaint->department_id}");


            $complaint->refresh();

        });
    }

    public function addMessage(int $complaintId, string $message, string $type = 'note'): array
    {
        $complaint = $this->repo->find($complaintId);
        if (!$complaint) {
            throw new \Exception('complaint not found');
        }

        $log = $this->statusRepo->createForComplaint(
            $complaint,
            $complaint->status,
            $message
        );

        $this->audit(
            'complaints',
            $type === 'note' ? 'add_note' : 'request_more_info',
            $type !== 'note' ? 'تمت إضافة ملاحظة للشكوى' : 'تم طلب معلومات إضافية',
            null,
            ['message' => $message]
        );

        Cache::forget("complaints_department_{$complaint->department_id}");

//        if ($complaint->user) {
//            if ($type === 'note') {
//                $complaint->user->notify(new ComplaintNoteAdded($complaint, $message));
//            } else {
//                $complaint->user->notify(new ComplaintMoreInfoRequested($complaint, $message));
//            }
//        }

        return [
            'complaint_id' => $complaint->id,
            'type' => $type,
            'message' => $message,
            'log_id' => $log->id,
            'status' => $complaint->status
        ];
    }
    public function getComplaintsForCitizen(): Collection
    {
        $citizenId = auth()->id();

        if(is_null($citizenId)) {
            return collect();

        }
        return $this->repo->getuserComplaints($citizenId);
    }

    public function find(int $id)
    {
        $employee = auth()->user();
        $departmentId = $employee->department_id;

        return $this->repo->query()
            ->with('user') // تضمين بيانات المستخدم
            ->where('id', $id)
            ->where('department_id', $departmentId)
            ->first();
    }
}
