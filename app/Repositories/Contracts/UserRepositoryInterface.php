<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;
    public function create(array $data): User;
    public function resetFailedAttempts(User $user): void;
    public function increaseFailedAttempts(User $user): void;
    public function lock(User $user, int $minutes): void;
    public function save(User $user): void;
}
