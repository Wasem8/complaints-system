<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserManagementRepositoryInterface;

class UserManagementRepository implements UserManagementRepositoryInterface
{
    public function all()
    {
        return User::with('department')->get();
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
}
