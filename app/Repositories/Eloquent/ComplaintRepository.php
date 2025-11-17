<?php

namespace App\Repositories\Eloquent;

use App\Models\Complaint;
use App\Models\Complaint_file;
use App\Models\ComplaintFile;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Contracts\ComplaintRepositoryInterface;

class ComplaintRepository implements ComplaintRepositoryInterface
{
    public function create(array $data): Complaint
    {
        $data['tracking_number'] = $this->generateTrackingNumber();

        return Complaint::create($data);
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
}
