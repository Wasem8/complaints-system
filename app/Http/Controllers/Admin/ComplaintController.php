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
        $request->validate(['status' => 'required|string']);

        $data = $this->service->updateStatus($id, $request->status);
        if (!$data) return Response::error(null, "Complaint not found", 404);

        return Response::success($data, "Status updated");
    }

    public function addNote(Request $request, $id)
    {
        $request->validate([
            'note' => 'required|string',
        ]);

        $note = [
            'note' => $request->note,
            'user_id' => auth()->id(),
        ];

        $data = $this->service->addNote($id, $note);
        if (!$data) return Response::error(null, "Complaint not found", 404);

        return Response::success($data, "Note added");
    }

    public function timeline($id)
    {
        $data = $this->service->timeline($id);

        if (!$data) return Response::error(null, "Complaint not found", 404);

        return Response::success($data);
    }

    public function archive($id)
    {
        $data = $this->service->archive($id);

        if (!$data) return Response::error(null, "Complaint not found", 404);

        return Response::success(null, "Complaint archived");
    }
}
