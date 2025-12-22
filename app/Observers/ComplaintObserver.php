<?php

namespace App\Observers;

use App\Events\AuditEvent;
use App\Models\Complaint;

class ComplaintObserver
{
    public function created(Complaint $complaint)
    {
        event(new AuditEvent(
            auth()->id(),
            'complaints',
            'created',
            'complaints created',
            null,
            $complaint->toArray()
        ));
    }

    public function updated(Complaint $complaint)
    {
        event(new AuditEvent(
            auth()->id(),
            'complaints',
            'updated',
            'complaints updated',
            $complaint->getOriginal(),
            $complaint->getChanges()
        ));
    }

    public function deleted(Complaint $complaint)
    {
        event(new AuditEvent(
            auth()->id(),
            'complaints',
            'deleted',
            'complaints deleted',
            $complaint->toArray(),
            null
        ));
    }
}
