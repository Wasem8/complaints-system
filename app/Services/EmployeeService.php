<?php

namespace App\Services;

use App\Repositories\Contracts\EmployeeRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class EmployeeService
{
    protected EmployeeRepositoryInterface $employees;
    protected AuditService $audit;


    public function __construct(EmployeeRepositoryInterface $employees, AuditService $audit)
    {
        $this->employees = $employees;
        $this->audit = $audit;
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
        $this->audit->log(
            module: 'employees',
            action: 'create',
            description: 'تم إنشاء موظف جديد',
            old: null,
            new: $user->toArray()
        );
        return $user;
    }
}
