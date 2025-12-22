<?php

namespace App\Services;

use App\Repositories\Contracts\DepartmentRepositoryInterface;

class DepartmentService
{
    protected DepartmentRepositoryInterface $departments;
    protected AuditService $audit;

    public function __construct(DepartmentRepositoryInterface $departments,
        AuditService $audit
    )
    {
        $this->departments = $departments;
        $this->audit = $audit;
    }

    public function getAll()
    {
        return $this->departments->all();
    }

    public function create(array $data)
    {
        $department = $this->departments->create($data);

        return $department;
    }

    public function update(int $id, array $data)
    {
        $department = $this->departments->find($id);

        if (!$department) {
            return null;
        }

        $oldData = $department->toArray();

        $updated = $this->departments->update($department, $data);


        return $updated;
    }

    public function delete(int $id)
    {
        $department = $this->departments->find($id);

        if (!$department) {
            return null;
        }

        $oldData = $department->toArray();

        $this->departments->delete($department);


        return true;
    }
}
