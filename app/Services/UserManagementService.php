<?php

namespace App\Services;

use App\Repositories\Contracts\UserManagementRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserManagementService
{
    protected $repo;

    public function __construct(UserManagementRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function list()
    {
        return $this->repo->all();
    }

    public function find($id)
    {
        return $this->repo->find($id);
    }

    public function create(array $data)
    {
        if (($data['role'] ?? null) !== 'employee') {
            $data['department_id'] = null;
        }
        $data['password'] = Hash::make($data['password']);
        $data['email_verified_at'] = now();
        $data['status'] = 'active';
        $user = $this->repo->create($data);
        if (isset($data['role'])) {
            $user->assignRole($data['role']);
        }

        return $user;
    }

    public function update($id, array $data)
    {
        $user = $this->repo->find($id);
        if (!$user) return null;

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = $this->repo->update($user, $data);

        if (isset($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        return $user;
    }

    public function delete($id)
    {
        $user = $this->repo->find($id);
        if (!$user) return null;

        return $this->repo->delete($user);
    }

    public function updateStatus($id, string $status)
    {
        $user = $this->repo->find($id);
        if (!$user) return null;

        $user->status = $status;
        $user->save();

        return $user;
    }

}
