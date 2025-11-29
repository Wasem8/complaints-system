<?php

namespace App\Services;

use App\Repositories\Contracts\ComplaintTypeRepositoryInterface;
use Illuminate\Support\Collection;

class ComplaintTypeService
{

    protected ComplaintTypeRepositoryInterface $complaintRepo;

    public function __construct(
        ComplaintTypeRepositoryInterface $complaintTypeRepository
    )
    {
        $this->complaintRepo = $complaintTypeRepository;
    }

    public function getAllTypes(): array
    {
        return $this->complaintRepo->getAllTypes();
    }
}
