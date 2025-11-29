<?php

namespace App\Http\Controllers;

use App\Http\Responses\Response;
use App\Services\ComplaintTypeService;
use Illuminate\Http\Request;

class ComplaintTypeController extends Controller
{
    protected ComplaintTypeService $service;

    public function __construct(ComplaintTypeService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $type = $this->service->getAllTypes();

        return Response::Success($type,'type returned successfully',200);
    }
}
