<?php

namespace App\Services;

use App\Repositories\Contracts\ReportRepositoryInterface;
use PDF;
use Illuminate\Support\Facades\Storage;

class ReportService
{
    protected ReportRepositoryInterface $repo;

    public function __construct(ReportRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function dashboardStats(array $filters)
    {
        return [
            'complaints_stats' => $this->repo->complaintsStats($filters),
            'department_stats' => $this->repo->complaintsByDepartment($filters),
            'performance_stats' => $this->repo->performanceStats($filters),
        ];
    }

    public function activityLogs(array $filters)
    {
        return $this->repo->activityLogs($filters);
    }

    public function errorLogs(array $filters)
    {
        return $this->repo->errorLogs($filters);
    }

    public function exportCSV(array $filters)
    {
        $items = $this->repo->exportComplaintsToCSV($filters);

        $filename = 'complaints_' . time() . '.csv';
        $path = storage_path('app/' . $filename);

        $file = fopen($path, 'w');
        fputcsv($file, ['ID', 'Title', 'Status', 'Created At']);

        foreach ($items as $row) {
            fputcsv($file, [
                $row->id,
                $row->title,
                $row->status,
                $row->created_at
            ]);
        }

        fclose($file);
        return $filename;
    }

    public function exportPDF(array $filters, bool $forDownload = false)
    {
        $items = $this->repo->exportComplaintsToPDF($filters);

        $pdf = PDF::loadView('complaints', compact('items'));

        $filename = 'complaints_' . time() . '.pdf';


        $fullPath = storage_path('app/public/' . $filename);

        file_put_contents($fullPath, $pdf->output());

        if ($forDownload) {
            return $fullPath; 
        }

        return asset('storage/' . $filename);
    }


}
