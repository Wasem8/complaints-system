<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Responses\Response;
use App\Services\EmployeeService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    private EmployeeService $service;

    public function __construct(EmployeeService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $employees = $this->service->getAll();
        return Response::Success($employees,'employees',200);
    }

    public function store(StoreEmployeeRequest $request)
    {

        $validateData = $request->validated();

        $employee = $this->service->create($validateData);
        return Response::Success($employee,'add employee success',200);
    }
}

