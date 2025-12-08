<?php

namespace App\Repositories\Eloquent;

use App\Models\AuditLog;
use App\Repositories\Contracts\AuditLogRepositoryInterface;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\FuncCall;

class AuditLogRepository implements AuditLogRepositoryInterface
{

    public function log(
        ?int $user_id,
        string $module,
        string $action,
        string $description,
        ?array $old = null,
        ?array $new = null
    ): void {
        AuditLog::create([
            'user_id'     => $user_id ?? auth()->id(),
            'module'      => $module,
            'action'      => $action,
            'description' => $description,
            'old_values'  => $old,
            'new_values'  => $new,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->header('User-Agent'),
        ]);
    }


    public function filter(array $filters)
    {
        $query = AuditLog::query();

        if (!empty($filters['module'])) {

            $query->where('module', $filters['module']);
        }
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function find(int $id)
    {
        return AuditLog::find($id);
    }

    public function index()
    {
        return AuditLog::all();
    }
}
