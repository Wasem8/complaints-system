<?php

namespace App\Repositories\Contracts;

use App\Models\Complaint;
use App\Models\Complaint_status_log;
use Illuminate\Support\Collection;

interface ComplaintStatusRepositoryInterface {
    public function createForComplaint(
        Complaint $complaint,
        string $newStatus,
        ?string $note = null,
    ): Complaint_status_log;

    public function getByComplaintId(int $complaintId): Collection;
}
