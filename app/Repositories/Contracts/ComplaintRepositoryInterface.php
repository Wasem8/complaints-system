<?php

namespace App\Repositories\Contracts;

use App\Models\Complaint;
use Illuminate\Support\Collection;

interface ComplaintRepositoryInterface
{

    public function create(array $data): Complaint;
    public function addFiles(Complaint $complaint, array $files): void;
    public function find(int $id): ?Complaint;
    public function getByDepartment(int $departmentId): Collection;
    public function update(int $id, array $data): bool;
}
