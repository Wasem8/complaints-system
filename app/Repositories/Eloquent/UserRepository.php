<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function resetFailedAttempts(User $user): void
    {
        $user->failed_attempts = 0;
        $user->locked_until = null;
        $user->save();
    }

    public function increaseFailedAttempts(User $user): void
    {
        $user->failed_attempts += 1;
        $user->save();
    }

    public function lock(User $user, int $minutes): void
    {
        $user->locked_until = now()->addMinutes($minutes);
        $user->failed_attempts = 0;
        $user->save();
    }

    public function save(User $user): void
    {
        $user->save();
    }
}
