<?php

namespace App\Services;

use App\Repositories\Contracts\DepartmentRepositoryInterface;

class DepartmentService
{
    protected DepartmentRepositoryInterface $departments;

    public function __construct(DepartmentRepositoryInterface $departments)
    {
        $this->departments = $departments;
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

        $updated = $this->departments->update($department, $data);


        return $updated;
    }

    public function delete(int $id)
    {
        $department = $this->departments->find($id);

        if (!$department) {
            return null;
        }

        $this->departments->delete($department);


        return true;
    }
}
