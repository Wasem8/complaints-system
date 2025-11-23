<?php

namespace App\Services;

use App\Repositories\Contracts\EmployeeRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class EmployeeService
{
    protected EmployeeRepositoryInterface $employees;

    public function __construct(EmployeeRepositoryInterface $employees)
    {
        $this->employees = $employees;
    }

    public function getAll()
    {
        return $this->employees->allEmployees();
    }

    public function create(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $data['email_verified_at'] = now();
        $user = $this->employees->create($data);
        $user->assignRole('employee');
        return $user;
    }
}
