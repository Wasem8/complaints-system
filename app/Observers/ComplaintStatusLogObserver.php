<?php

namespace App\Observers;

use App\Events\AuditEvent;
use App\Models\Complaint_status_log;

class ComplaintStatusLogObserver
{
    /**
     * Handle the Complaint_status_log "created" event.
     */
    public function created(Complaint_status_log $complaint_status_log): void
    {
        event(new AuditEvent(
            auth()->id(),
            'Complaint_status_log',
            'add message',
            'Complaint status added',
            $complaint_status_log->getOriginal('note'),
            $complaint_status_log->toArray()
        ));
    }

    /**
     * Handle the Complaint_status_log "updated" event.
     */
    public function updated(Complaint_status_log $complaint_status_log): void
    {
        event(new AuditEvent(
            auth()->id(),
            'department',
            'Complaint_status_log',
            'Complaint status updated',
            $complaint_status_log->getOriginal(),
            $complaint_status_log->toArray()
        ));
    }

    /**
     * Handle the Complaint_status_log "deleted" event.
     */
    public function deleted(Complaint_status_log $complaint_status_log): void
    {
        event(new AuditEvent(
            auth()->id(),
            'Complaint_status_log',
            'deleted',
            'Complaint_status_log deleted',
            $complaint_status_log->getOriginal(),
            $complaint_status_log->toArray()
        ));
    }

    /**
     * Handle the Complaint_status_log "restored" event.
     */
    public function restored(Complaint_status_log $complaint_status_log): void
    {
        //
    }

    /**
     * Handle the Complaint_status_log "force deleted" event.
     */
    public function forceDeleted(Complaint_status_log $complaint_status_log): void
    {
        //
    }
}
