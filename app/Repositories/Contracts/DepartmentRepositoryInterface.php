<?php

namespace App\Repositories\Contracts;

use App\Models\Department;

interface DepartmentRepositoryInterface
{
    public function all();
    public function find(int $id): ?Department;
    public function create(array $data): Department;
    public function update(Department $department, array $data): Department;
    public function delete(Department $department): bool;
}
