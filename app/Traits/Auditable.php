<?php

namespace App\Traits;

use App\Services\AuditService;

trait Auditable
{
    public function audit(
        string $module,
        string $action,
        string $description,
        ?array $old = null,
        ?array $new = null
    ): void {
        app(AuditService::class)->log(
            module: $module,
            action: $action,
            description: $description,
            old: $old,
            new: $new
        );
    }
}
