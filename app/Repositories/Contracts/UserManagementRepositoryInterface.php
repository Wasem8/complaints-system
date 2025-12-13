<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserManagementRepositoryInterface
{
    public function all(array $filters);
    public function find(int $id): ?User;
    public function create(array $data): User;
    public function update(User $user, array $data): User;
    public function delete(User $user): bool;
    public function getByRole(string $role);
    public function searchUser(string $query);
}
