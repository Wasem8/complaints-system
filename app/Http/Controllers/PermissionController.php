<?php

namespace App\Http\Controllers;

use App\Http\Responses\Response;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    private PermissionService $service;

    public function __construct(PermissionService $service)
    {
        $this->service = $service;
    }

    public function roles()
    {
        $data = $this->service->roles();
        return Response::success($data,'success');
    }

    public function permissions()
    {
        $data = $this->service->permissions();
        return Response::success($data,'success');
    }

    public function createRole(Request $request)
    {
        $request->validate(['name' => 'required']);
        $data = $this->service->createRole($request->name);
        return Response::success($data,'success');
    }

    public function createPermission(Request $request)
    {
        $request->validate(['name' => 'required']);
        $data = $this->service->createPermission($request->name);
        return Response::success($data,'success');
    }

    public function assignPermissions(Request $request, $role)
    {
        $request->validate(['permissions' => 'required|array']);
        $data = $this->service->assignPermissions($role,$request->permissions);
        return Response::success($data, 'Role updated');
    }
}

