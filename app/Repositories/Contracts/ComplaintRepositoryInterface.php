<?php

namespace App\Repositories\Contracts;

use App\Models\Complaint;

interface ComplaintRepositoryInterface
{

    public function create(array $data): Complaint;
    public function addFiles(Complaint $complaint, array $files): void;

}
