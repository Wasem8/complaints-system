<?php

namespace App\Repositories\Contracts;

interface ReportRepositoryInterface
{
    public function complaintsStats(array $filters);
    public function complaintsByDepartment(array $filters);
    public function performanceStats(array $filters);

    public function activityLogs(array $filters);
    public function errorLogs(array $filters);

    public function exportComplaintsToCSV(array $filters);
    public function exportComplaintsToPDF(array $filters);
}
