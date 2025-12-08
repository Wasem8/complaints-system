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

        $this->audit->log(
            module: 'departments',
            action: 'create',
            description: 'تم إنشاء قسم جديد',
            old: null,
            new: $department->toArray()
        );
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

        $this->audit->log(
            module: 'departments',
            action: 'update',
            description: 'تم تحديث بيانات القسم',
            old: $oldData,
            new: $updated->toArray()
        );

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

        $this->audit->log(
            module: 'departments',
            action: 'delete',
            description: 'تم حذف القسم',
            old: $oldData,
            new: null
        );

        return true;
    }
}
