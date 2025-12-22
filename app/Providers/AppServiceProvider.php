<?php

namespace App\Providers;


use App\Models\AuditLog;
use App\Models\Complaint;
use App\Models\Department;
use App\Models\User;
use App\Observers\ComplaintObserver;
use App\Observers\DepartmentObserver;
use App\Observers\UserObserver;
use App\Repositories\Contracts\AuditLogRepositoryInterface;

use App\Repositories\Contracts\AdminComplaintRepositoryInterface;

use App\Repositories\Contracts\ComplaintRepositoryInterface;
use App\Repositories\Contracts\ComplaintStatusRepositoryInterface;
use App\Repositories\Contracts\PermissionRepositoryInterface;

use App\Repositories\Eloquent\AuditLogRepository;

use App\Repositories\Contracts\ReportRepositoryInterface;
use App\Repositories\Eloquent\AdminComplaintRepository;
use App\Repositories\Eloquent\ComplaintRepository;
use App\Repositories\Eloquent\ComplaintStatusRepository;
use App\Repositories\Eloquent\PermissionRepository;
use App\Repositories\Eloquent\ReportRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            \App\Repositories\Eloquent\UserRepository::class
        );
        $this->app->bind(
            ComplaintRepositoryInterface::class,
            ComplaintRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\DepartmentRepositoryInterface::class,
            \App\Repositories\Eloquent\DepartmentRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\EmployeeRepositoryInterface::class,
            \App\Repositories\Eloquent\EmployeeRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\UserManagementRepositoryInterface::class,
            \App\Repositories\Eloquent\UserManagementRepository::class
        );

        $this->app->bind(
            PermissionRepositoryInterface::class,
            PermissionRepository::class
        );

        $this->app->bind(
            AdminComplaintRepositoryInterface::class,
            AdminComplaintRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\AuditLogRepositoryInterface::class,
            \App\Repositories\Eloquent\AuditLogRepository::class
        );


        $this->app->bind(
            ReportRepositoryInterface::class,
            ReportRepository::class
        );

        $this->app->bind(
            ComplaintStatusRepositoryInterface::class,
            ComplaintStatusRepository::class
        );

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Complaint::observe(ComplaintObserver::class);
        Department::observe(DepartmentObserver::class);
    }
}
