<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\Response;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected ReportService $service;

    public function __construct(ReportService $service)
    {
        $this->service = $service;
    }

    public function dashboard(Request $request)
    {
        $data = $this->service->dashboardStats($request->all());
        return Response::Success($data,'success');
    }

    public function logs(Request $request)
    {
        return response()->json(
            $this->service->activityLogs($request->all())
        );
    }

    public function errorLogs(Request $request)
    {
        return response()->json(
            $this->service->errorLogs($request->all())
        );
    }

    public function exportCSV(Request $request)
    {
        $file = $this->service->exportCSV($request->all());

        return response()->download(storage_path("app/$file"));
    }
    public function exportPDF(Request $request)
    {
        $file = $this->service->exportPDF($request->all(), true);

        if (!file_exists($file)) {
            abort(404, "File not found");
        }

        return response()->download($file);
    }

}
