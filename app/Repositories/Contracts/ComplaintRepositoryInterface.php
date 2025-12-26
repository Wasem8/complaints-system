<?php

namespace App\Repositories\Contracts;

use App\Models\Complaint;
use Illuminate\Support\Collection;

interface ComplaintRepositoryInterface
{

    public function create(array $data): Complaint;

    public function updateComplaint(array $data, $complaint): Complaint;
    public function addFiles(Complaint $complaint, array $files): void;
    public function find(int $id): ?Complaint;
    public function getByDepartment(int $departmentId): Collection;
    public function updateStatus(Complaint $complaint, string $status): Complaint;
    public function getuserComplaints(int $userId): Collection;
    public function query();
}
