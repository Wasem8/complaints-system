<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ComplaintRequest;
use App\Services\ComplaintService;

class ComplaintController extends Controller
{
    public function __construct(private ComplaintService $service) {}

    public function store(ComplaintRequest $request)
    {
        $data = $request->only([
            'type',
            'authority',
            'description',
            'location_text',
        ]);

        $files = $request->file('files');

        if($files && !is_array($files)) {
            $files = [$files];
        }

        $result = $this->service->submit($data, $files ?? []);

        return response()->json($result, $result['status'] ? 201 : 400);
    }
}
