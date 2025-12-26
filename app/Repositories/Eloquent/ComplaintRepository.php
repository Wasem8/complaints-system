<?php

namespace App\Repositories\Eloquent;

use App\Models\Complaint;
use App\Models\Complaint_file;
use App\Models\ComplaintFile;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Contracts\ComplaintRepositoryInterface;
use Illuminate\Support\Collection;

class ComplaintRepository implements ComplaintRepositoryInterface
{
    public function create(array $data): Complaint
    {
        $data['tracking_number'] = $this->generateTrackingNumber();

        return Complaint::create($data);
    }

    public function updateComplaint(array $data, $complaint): Complaint
    {
        $complaint->update($data);

        return $complaint;
    }
    public function addFiles(Complaint $complaint, array $files): void
    {
        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $path = $file->store('complaints/' . $complaint->id, 'public');

            Complaint_file::create([
                'complaint_id' => $complaint->id,
                'file_path' => $path,
                'file_type' => $file->getClientOriginalExtension(),
            ]);
        }
    }

    private function generateTrackingNumber(): string
    {
        return 'CMP-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(6));
    }

    public function find(int $id): ?Complaint
    {
        return Complaint::with('statusLogs', 'files')->findOrFail($id);
    }

    public function getByDepartment(int $departmentId): Collection
    {
        return Complaint::with('user','statusLogs','files')
        ->where('department_id',$departmentId)
        ->orderBy('created_at')
        ->get();
    }

    public function updateStatus(Complaint $complaint, string $status): Complaint
    {
        $oldStatus = $complaint->status;
        $complaint->status = $status;
        $complaint->save();
        $complaint->statusLogs()->create([
            'old_status' => $oldStatus,
            'new_status' => $status,
        ]);
        return $complaint;
    }

    public function getuserComplaints(int $userId): Collection
    {
        return Complaint::where('user_id',$userId)
        ->with('files')
        ->orderBy('created_at')
        ->get();
    }
    public function query()
    {
        return Complaint::query();
    }
}
