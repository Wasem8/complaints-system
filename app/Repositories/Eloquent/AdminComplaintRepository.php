<?php

namespace App\Repositories\Eloquent;

use App\Models\Complaint;
use App\Repositories\Contracts\AdminComplaintRepositoryInterface;

class AdminComplaintRepository implements AdminComplaintRepositoryInterface
{
    public function all(array $filters = [])
    {
        $query = Complaint::with(['user']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (isset($filters['from']) && isset($filters['to'])) {
            $query->whereBetween('created_at', [$filters['from'], $filters['to']]);
        }

        return $query->latest()->paginate(20);
    }

    public function find(int $id): ?Complaint
    {
        return Complaint::with(['user', 'files'])->find($id);
    }

    public function updateStatus(Complaint $complaint, string $status): Complaint
    {
        $oldStatus = $complaint->status;
        $complaint->status = $status;
        $complaint->save();
        $complaint->statusLogs()->create([
            'old_status' => $oldStatus,
            'new_status' => $status,
        ]);
        return $complaint;
    }


    public function getTimeline(Complaint $complaint)
    {
        return [
            'status_logs' => $complaint->statusLogs()->latest()->get(),
        ];
    }

}
