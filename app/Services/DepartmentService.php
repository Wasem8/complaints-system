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
        return $this->departments->create($data);
    }

    public function update(int $id, array $data)
    {
        $department = $this->departments->find($id);

        if (!$department) {
            return null;
        }

        return $this->departments->update($department, $data);
    }

    public function delete(int $id)
    {
        $department = $this->departments->find($id);

        if (!$department) {
            return null;
        }

        return $this->departments->delete($department);
    }
}
