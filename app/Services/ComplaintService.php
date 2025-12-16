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
use Illuminate\Support\Facades\Redis;
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

        $this->audit(
            module: 'complaints',
            action: 'create',
            description: 'تم إنشاء الشكوى',
            old: null,
            new: $complaint->toArray()
        );

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

        return Cache::remember("complaints_department_{$departmentId}", 60, function () use ($departmentId) {
            return $this->repo->getByDepartment($departmentId);
        });
    }


    public function updateStatus(int $complaintId, string $newStatus, ?string $note = null)
    {
        Cache::lock('complaint_lock_' . $complaintId, 10)->block(5, function () use ($complaintId, $newStatus, $note) {
            $complaint = $this->repo->find($complaintId);

            if (!$complaint) {
                throw new \Exception("الشكوى غير موجودة");
            }


            $oldStatus = $complaint->status;

            if($oldStatus === $newStatus) {
                throw new \Exception('الحالة نفسها، لا يوجد تغيير');
            }

            $finalStatuses = ['done', 'rejected'];

            if (in_array($oldStatus, $finalStatuses, true)) {
                throw new \Exception('انتهت معالجة الشكوى');
            }


            $this->repo->update($complaint->id, ['status' => $newStatus]);

            Cache::forget("complaints_department_{$complaint->department_id}");


            $complaint->refresh();
            $this->statusRepo->createForComplaint(
                $complaint,
                $newStatus,
                $note ?? 'تم تحديث الحالة',
                $oldStatus,
            );

            $this->audit(
                'complaints',
                'update_status',
                'تم تحديث حالة الشكوى',
                ['old_status' => $oldStatus],
                ['new_status' => $newStatus]
            );

            if ($complaint->user) {
                $complaint->user->notify(new ComplaintStatusUpdated($complaint, $newStatus));
            }
        });
    }

    public function addMessage(int $complaintId, string $message, string $type = 'note'): array
    {
        $complaint = $this->repo->find($complaintId);
        if (!$complaint) {
            throw new \Exception('الشكوى غير موجودة');
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

        if ($complaint->user) {
            if ($type === 'note') {
                $complaint->user->notify(new ComplaintNoteAdded($complaint, $message));
            } else {
                $complaint->user->notify(new ComplaintMoreInfoRequested($complaint, $message));
            }
        }

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
            ->where('id', $id)
            ->where('department_id', $departmentId)
            ->first();
        }

}
