<?php

namespace App\Services;

use App\Repositories\Contracts\UserManagementRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserManagementService
{
    protected $repo;
    protected AuditService $audit;

    public function __construct(UserManagementRepositoryInterface $repo, AuditService $audit)
    {
        $this->repo = $repo;
        $this->audit = $audit;
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
        $data['password'] = Hash::make($data['password']);

        $user = $this->repo->create($data);

        if (isset($data['role'])) {
            $user->assignRole($data['role']);
        }
        $this->audit->log(
            module: 'users',
            action: 'create',
            description: 'تم إنشاء مستخدم جديد',
            old: null,
            new: $user->toArray()
        );
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
        $old = $user->toArray();
        $this->audit->log(
            module: 'users',
            action: 'update',
            description: 'تم تعديل بيانات المستخدم',
            old: $old,
            new: $user->toArray()
        );

        return $user;
    }

    public function delete($id)
    {
        $user = $this->repo->find($id);
        if (!$user) return null;

        $old = $user->toArray();
        $this->audit->log(
            module: 'users',
            action: 'delete',
            description: 'تم حذف المستخدم',
            old: $old,
            new: null
        );

        return $this->repo->delete($user);
    }
}
