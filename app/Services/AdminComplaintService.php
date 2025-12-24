<?php

namespace App\Services;

use App\Repositories\Contracts\AdminComplaintRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class AdminComplaintService
{
    protected AdminComplaintRepositoryInterface $complaints;

    public function __construct(AdminComplaintRepositoryInterface $complaints)
    {
        $this->complaints = $complaints;
    }

    public function list(array $filters)
    {
        return $this->complaints->all($filters);
    }

    public function find(int $id)
    {
        return $this->complaints->find($id);
    }

    public function updateStatus(int $id, string $status)
    {
        return Cache::lock('complaint_lock_' . $id, 10)->block(5, function () use ($id, $status) {
            $complaint = $this->complaints->find($id);

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

             $this->complaints->updateStatus($complaint, $status);

            Cache::forget("complaints_department_{$complaint->department_id}");


            $complaint->refresh();
        });
    }




    public function timeline(int $id)
    {
        $complaint = $this->complaints->find($id);
        if (!$complaint) return null;

        return $this->complaints->getTimeline($complaint);
    }


}
