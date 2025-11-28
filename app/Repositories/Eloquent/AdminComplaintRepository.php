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
        $complaint->status = $status;
        $complaint->save();
        return $complaint;
    }

    public function addNote(Complaint $complaint, array $data): Complaint
    {
        $complaint->notes()->create($data);
        return $complaint->load('notes');
    }

    public function getTimeline(Complaint $complaint)
    {
        return [
            'status_logs' => $complaint->statusLogs()->latest()->get(),
            'notes'       => $complaint->notes()->latest()->get(),
        ];
    }

    public function archive(Complaint $complaint): bool
    {
        $complaint->status = 'archived';
        return $complaint->save();
    }
}
