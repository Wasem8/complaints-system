<?php

namespace App\Listeners;

use App\Events\AuditEvent;
use App\Models\AuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AuditListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AuditEvent $event): void
    {
        AuditLog::create([
            'user_id' => $event->userId ?? auth()->id(),
            'module' => $event->module,
            'action' => $event->action,
            'description' => $event->description,
            'old_values' => $event->oldValues,
            'new_values' => $event->newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
