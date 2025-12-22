<?php

namespace App\Observers;

use App\Events\AuditEvent;
use App\Models\User;

class UserObserver
{
    public function created(User $user)
    {
        event(new AuditEvent(
            auth()->id() ?? $user->id,
            'users',
            'create',
            'user created successfully',
            null,
            $user->toArray()
        ));
    }

    public function updated(User $user)
    {
        event(new AuditEvent(
            auth()->id(),
            'users',
            'update',
            'update user successfully',
            $user->getOriginal(),
            $user->getChanges()
        ));
    }

    public function deleted(User $user)
    {
        event(new AuditEvent(
            auth()->id(),
            'users',
            'delete',
            'delete user successfully',
            $user->toArray(),
            null
        ));
    }
}
