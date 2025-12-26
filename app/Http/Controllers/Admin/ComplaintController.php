<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\Response;
use App\Services\AdminComplaintService;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    protected AdminComplaintService $service;

    public function __construct(AdminComplaintService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'department_id', 'from', 'to']);
        return Response::success($this->service->list($filters),'success');
    }

    public function show($id)
    {
        $data = $this->service->find($id);
        if (!$data) return Response::error(null, "Complaint not found", 404);

        return Response::success($data,'success');
    }

    public function updateStatus(Request $request, $id)
    {
        try {
        $request->validate(['status' => 'required|string']);
        $data = $this->service->updateStatus($id,$request->status);
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة الشكوى بنجاح',
                'updated_by' => [
                    'id'   => auth()->id(),
                    'name' => auth()->user()->name,
                ]
            ]);
        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'updated_by' => [
                    'id'   => auth()->id(),
                    'name' => auth()->user()->name,
                ]
            ], 422);
        }
    }



    public function timeline($id)
    {
        $data = $this->service->timeline($id);

        if (!$data) return Response::error(null, "Complaint not found", 404);

        return Response::success($data,'success');
    }


}
