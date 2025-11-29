<?php

namespace App\Providers;

use App\Repositories\Contracts\ComplaintRepositoryInterface;
use App\Repositories\Contracts\ComplaintTypeRepositoryInterface;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use App\Repositories\Eloquent\ComplaintRepository;
use App\Repositories\Eloquent\ComplaintTypeRepository;
use App\Repositories\Eloquent\PermissionRepository;
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
            \App\Repositories\Contracts\ComplaintStatusRepositoryInterface::class,
            \App\Repositories\Eloquent\ComplaintStatusRepository::class,
        );

        $this->app->bind(
            PermissionRepositoryInterface::class,
            PermissionRepository::class
        );

        $this->app->bind(
            ComplaintTypeRepositoryInterface::class,
            ComplaintTypeRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
