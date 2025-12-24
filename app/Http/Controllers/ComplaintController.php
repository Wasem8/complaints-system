<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ComplaintRequest;
use App\Http\Requests\UpdateComplaintRequest;
use App\Http\Requests\UpdateStatusRequest;
use App\Http\Responses\Response;
use App\Services\ComplaintService;
use App\Services\ComplaintStatusService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function __construct(
        private ComplaintService $service,
        private ComplaintStatusService $statusService,
    ) {}


    public function store(ComplaintRequest $request)
    {
        $data = $request->only([
            'type',
            'department_id',
            'description',
            'location_text',
        ]);

        $files = $request->file('files');

        if($files && !is_array($files)) {
            $files = [$files];
        }

        $result = $this->service->submit($data, $files ?? []);

        return response()->json($result, $result['status'] ? 201 : 400);
    }

    public function update(UpdateComplaintRequest $request, int $id)
    {
        $data = $request->only([
        'type',
        'department_id',
        'description',
        'location_text',
    ]);

        $files = $request->file('files');

        if($files && !is_array($files)) {
            $files = [$files];
        }

        $result = $this->service->updateComplaint($id,$data, $files ?? []);

        return response()->json($result, $result['status'] ? 201 : 400);
    }


    public function index()
    {
        $complaints = $this->service->getDepartmentComplaints();

        return response()->json([
            'success' => true,
            'data' => $complaints
        ]);
    }

    public function getComplaintById(int $id)
    {
        $data = $this->service->find($id);
        if (!$data) return Response::error(null, "Complaint not found", 404);

        return Response::success($data, 'success');
    }
    public function show(int $id)
    {
        try {
            $statusLogs = $this->statusService->getStatusTimeLine($id);

            return response()->json([
                'status' => 1,
                'message' => 'success',
                'data' => [
                    'status_logs' => $statusLogs
                ]
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Complaint not found',
                'data' => null
            ], 404);
        }
    }

    public function updateStatus(UpdateStatusRequest $request, int $id)
    {


        try {
            $this->service->updateStatus(
                $id,
                $request->status,
                $request->note
            );

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
    public function addMessageToComplaint(Request $request, int $id)
    {
        $request->validate([
            'message' => 'required|string',
            'type' => 'required|in:note,more_info',
        ]);

        $data = $this->service->addMessage(
            $id,
            $request->message,
            $request->type
        );

        return Response::success(
            $data,
            $request->type === 'note'
                ? 'تمت إضافة الملاحظة بنجاح'
                : 'تم إرسال طلب المعلومات بنجاح'
        );
    }
    public function getAllcomplaint() {

        $complaints = $this->service->getComplaintsForCitizen();

        return Response::Success($complaints,'complaints get successfully',200);
    }
}
