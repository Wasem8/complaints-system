<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\EmployeeRepositoryInterface;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    public function allEmployees()
    {
        return User::role('employee')->get();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

}
