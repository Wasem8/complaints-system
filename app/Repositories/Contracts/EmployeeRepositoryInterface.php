<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface EmployeeRepositoryInterface
{
    public function allEmployees();
    public function create(array $data): User;
}
