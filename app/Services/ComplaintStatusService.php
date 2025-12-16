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

    public function getStatusTimeLine(int $complaintId): array
    {
        $logs = $this->statusRepo->getByComplaintId($complaintId);

        return $logs->map(function ($log) {
            return [
                'id'           => $log->id,
                'complaint_id' => $log->complaint_id,
                'new_status'   => $log->new_status,
                'note'         => $log->note,
                'created_at'   => $log->created_at->toDateTimeString(),
                'updated_at'   => $log->updated_at->toDateTimeString(),
            ];
        })->toArray();
    }
}

