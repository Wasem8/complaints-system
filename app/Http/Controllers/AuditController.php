<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Services\AuditService;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    protected $service;

    public function __construct(AuditService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
       return $this->service->index();
    }

    public function filter(Request $request)
    {
        return $this->service->filter($request->all());
    }

    public function show($id)
    {
        return $this->service->find($id);
    }

}
