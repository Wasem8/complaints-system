<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Repositories\Contracts\AuditLogRepositoryInterface;

class AuditService
{
    protected $repo;

    public function __construct(AuditLogRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }


    public function log(
        string $module,
        string $action,
        string $description,
        ?array $old = null,
        ?array $new = null
    ): void {
        $this->repo->log(
            auth()->id(),
            $module,
            $action,
            $description,
            $old,
            $new
        );
    }



    public function filter(array $filters)
    {
        return $this->repo->filter($filters);
    }

    public function find($id) {
        return $this->repo->find((int) $id);
    }

    public function index() {
        return $this->repo->index();
    }

}
