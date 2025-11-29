<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface ComplaintTypeRepositoryInterface {

    public function getAllTypes(): array;
}
