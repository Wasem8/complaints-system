<?php

namespace App\Observers;

use App\Events\AuditEvent;
use App\Models\Department;

class DepartmentObserver
{
    /**
     * Handle the Department "created" event.
     */
    public function created(Department $department): void
    {
        event(new AuditEvent(
            auth()->id(),
            'department',
            'created',
            'department created',
            null,
            $department->toArray()
        ));
    }

    /**
     * Handle the Department "updated" event.
     */
    public function updated(Department $department): void
    {
        event(new AuditEvent(
            auth()->id(),
            'department',
            'updated',
            'department updated',
            null,
            $department->toArray()
        ));
    }

    /**
     * Handle the Department "deleted" event.
     */
    public function deleted(Department $department): void
    {
        event(new AuditEvent(
            auth()->id(),
            'department',
            'deleted',
            'department deleted',
            null,
            $department->toArray()
        ));
    }

    /**
     * Handle the Department "restored" event.
     */
    public function restored(Department $department): void
    {
        //
    }

    /**
     * Handle the Department "force deleted" event.
     */
    public function forceDeleted(Department $department): void
    {
        //
    }
}
