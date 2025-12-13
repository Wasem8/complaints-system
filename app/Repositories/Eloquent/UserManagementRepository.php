<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserManagementRepositoryInterface;

class UserManagementRepository implements UserManagementRepositoryInterface
{
    public function all(array $filters)
    {
        $query = User::query()->with('department');

        if (isset($filters['role'])) {
            $query->role($filters['role']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        return $query->get();
    }

    public function find(int $id): ?User
    {
        return User::find($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user;
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function getByRole(string $role)
    {
        return User::role($role)->get();
    }

    public function searchUser(string $query)
    {
        return User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->get();
    }

}
