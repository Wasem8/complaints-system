<?php

namespace App\Repositories\Contracts;

interface AuditLogRepositoryInterface
{
    public function log(
        ?int $user_id,
        string $module,
        string $action,
        string $description,
        ?array $old = null,
        ?array $new = null
    ): void;


    public function filter(array $filters);

    public function find(int $id);


    public function index();
}
