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

        return $this->complaints->updateStatus($complaint, $status);
    }

    public function addNote(int $id, array $data)
    {
        $complaint = $this->complaints->find($id);
        if (!$complaint) return null;

        return $this->complaints->addNote($complaint, $data);
    }

    public function timeline(int $id)
    {
        $complaint = $this->complaints->find($id);
        if (!$complaint) return null;

        return $this->complaints->getTimeline($complaint);
    }

    public function archive(int $id)
    {
        $complaint = $this->complaints->find($id);
        if (!$complaint) return null;

        return $this->complaints->archive($complaint);
    }
}
