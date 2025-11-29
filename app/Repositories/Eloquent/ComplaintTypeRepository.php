<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\ComplaintTypeRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ComplaintTypeRepository implements ComplaintTypeRepositoryInterface
{
    public function getAllTypes(): array
    {
        $column = DB::select("SHOW COLUMNS FROM complaints LIKE 'type'")[0];

        preg_match("/^enum\('(.*)'\)$/", $column->Type, $matches);

        return explode("','", $matches[1]);
    }
}
