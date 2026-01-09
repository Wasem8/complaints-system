<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Responses\Response;
use App\Services\DepartmentService;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    private DepartmentService $service;

    public function __construct(DepartmentService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $data = $this->service->getAll();
        return Response::success($data,'success',200);
    }

    public function store(StoreDepartmentRequest $request)
    {
        $validatedData = $request->validated();

        $data = $this->service->create($validatedData);

        return Response::Success($data,'store department success', 201);
    }

    public function update(UpdateDepartmentRequest $request, $id)
    {
        $validatedData = $request->validated();
        $updated = $this->service->update($id, $validatedData);

        if (!$updated)
            return Response::Error(null,'Department not found',404);

        return Response::Success($updated,'update department success', 200);
    }

    public function destroy($id)
    {

        $deleted = $this->service->delete($id);

        if (!$deleted) {
            return Response::Error(null, 'Department not found', 404);
        }
        return Response::Success($deleted,'delete department success', 204);
    }
}

