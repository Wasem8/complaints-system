<?php

namespace App\Repositories\Eloquent;

use App\Models\AuditLog;
use App\Models\Complaint;
use App\Models\User;
use App\Models\ActivityLog;
use App\Repositories\Contracts\ReportRepositoryInterface;

class ReportRepository implements ReportRepositoryInterface
{
    public function complaintsStats(array $filters)
    {
        $baseQuery = Complaint::query();

        if (isset($filters['from']) && isset($filters['to'])) {
            $baseQuery->whereBetween('created_at', [$filters['from'], $filters['to']]);
        }

        return [
            'total' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'processing' => (clone $baseQuery)->where('status', 'processing')->count(),
            'done' => (clone $baseQuery)->where('status', 'done')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
        ];
    }


    public function complaintsByDepartment(array $filters)
    {
        return Complaint::selectRaw('department_id, COUNT(*) as total')
            ->groupBy('department_id')
            ->with('department')
            ->get();
    }

    public function performanceStats(array $filters)
    {
        return User::withCount([
            'handledComplaints as resolved_complaints' => function ($q) {
                $q->where('status', 'done');
            }
        ])->get();
    }

    public function activityLogs(array $filters)
    {
        return AuditLog::latest()->paginate(30);
    }

    public function errorLogs(array $filters)
    {
        return ActivityLog::where('type', 'error')->latest()->paginate(30);
    }

    public function exportComplaintsToCSV(array $filters)
    {
        return Complaint::all();
    }

    public function exportComplaintsToPDF(array $filters)
    {
        return Complaint::all();
    }
}
