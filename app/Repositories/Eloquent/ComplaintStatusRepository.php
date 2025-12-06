<?php

namespace App\Repositories\Eloquent;

use App\Models\Complaint;
use App\Models\Complaint_status_log;
use App\Models\ComplaintStatusLog;
use App\Repositories\Contracts\ComplaintStatusRepositoryInterface;
use Illuminate\Support\Collection;

class ComplaintStatusRepository implements ComplaintStatusRepositoryInterface
{

    public function createForComplaint(
        Complaint $complaint,
        string $newStatus,
        ?string $note = null
    ): Complaint_status_log {
        return Complaint_status_log::create([
            'complaint_id' => $complaint->id,
            'new_status'   => $newStatus,
            'note'         => $note,
        ]);
    }

    public function getByComplaintId(int $complaintId): Collection
    {
        return Complaint_status_log::where('complaint_id', $complaintId)
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
