<?php

namespace App\Services;

use App\Repositories\Contracts\ComplaintRepositoryInterface;

class ComplaintService
{
    public function __construct(
        private ComplaintRepositoryInterface $repo
    ) {}

    public function submit(array $data, array $files = []): array
    {
        $data['user_id'] = auth()->id();

        $complaint = $this->repo->create($data);

        if (!empty($files)) {
            $this->repo->addFiles($complaint, $files);
        }
        return [
            'status' => true,
            'message' => 'Complaint submitted successfully',
            'tracking_number' => $complaint->tracking_number,
            'complaint_id' => $complaint->id
        ];
    }
}
