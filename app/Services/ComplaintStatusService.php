<?php

namespace App\Services;

use App\Models\Complaint_status_log;
use App\Repositories\Contracts\ComplaintRepositoryInterface;
use App\Repositories\Contracts\ComplaintStatusRepositoryInterface;
use App\Repositories\Eloquent\ComplaintRepository;

class ComplaintStatusService
{
    public function __construct(
        private ComplaintRepositoryInterface $complaintRepo,
        private ComplaintStatusRepositoryInterface  $statusRepo
    )
    {}

    public function getStatusTimeLine(int $complaintId): array {
        $complaint = $this->complaintRepo->find($complaintId);

        $logs = $this->statusRepo->getByComplaintId($complaintId);
        return [
            'id'             => $complaint->id,
            'new_status' => $complaint->status,
            'history'        => $logs->map(function ($log) {
                return [
                    'status' => $log->new_status,
                    'time'   => $log->created_at->toDateTimeString(),
                    'note'   => $log->note,
                ];
            }),
            'last_update' => $logs->last()?->new_status
        ];

    }

}

