<?php

namespace App\Repositories\Contracts;

use App\Models\Complaint;

interface AdminComplaintRepositoryInterface
{
    public function all(array $filters = []);
    public function find(int $id): ?Complaint;
    public function updateStatus(Complaint $complaint, string $status): Complaint;
    public function getTimeline(Complaint $complaint);

}
