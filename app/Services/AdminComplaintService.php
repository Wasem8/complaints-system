<?php

namespace App\Services;

use App\Repositories\Contracts\AdminComplaintRepositoryInterface;

class AdminComplaintService
{
    protected AdminComplaintRepositoryInterface $complaints;

    public function __construct(AdminComplaintRepositoryInterface $complaints)
    {
        $this->complaints = $complaints;
    }

    public function list(array $filters)
    {
        return $this->complaints->all($filters);
    }

    public function find(int $id)
    {
        return $this->complaints->find($id);
    }

    public function updateStatus(int $id, string $status)
    {
        $complaint = $this->complaints->find($id);
        if (!$complaint) return null;

        $allowedTransitions = [
            'pending' => ['processing', 'rejected'],
            'processing' => ['done', 'rejected'],
            'done' => [],
            'rejected' => [],
        ];

        $current = $complaint->status;

        if (!isset($allowedTransitions[$current])) {
            return ['error' => 'Invalid current status'];
        }

        if (!in_array($status, $allowedTransitions[$current])) {
            return ['error' => "Cannot change status from {$current} to {$status}"];
        }

        if ($status === 'done' || $status === 'rejected') {
            $complaint->handled_by = auth()->id();
        }

        return $this->complaints->updateStatus($complaint, $status);
    }



    public function timeline(int $id)
    {
        $complaint = $this->complaints->find($id);
        if (!$complaint) return null;

        return $this->complaints->getTimeline($complaint);
    }


}
