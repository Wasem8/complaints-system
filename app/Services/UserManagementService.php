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

    public function filterUsers(array $filters)
    {
        return $this->repo->all($filters);
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

    public function updateStatus($id, string $status)
    {
        $user = $this->repo->find($id);
        if (!$user) return null;

        $user->status = $status;
        $user->save();

        return $user;
    }

}
