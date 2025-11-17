<?php

namespace App\Http\Responses;

use Illuminate\Pagination\LengthAwarePaginator;

class Response
{
    /**
     * Return a successful JSON response.
     */
    public static function Success($data, string $message, int $code = 200): \Illuminate\Http\JsonResponse
    {
        $response = [
            'status' => 1,
            'message' => $message,
            'data' => $data ?? (object)[],
        ];


        if ($data instanceof LengthAwarePaginator) {
            $response['data'] = $data->items();
            $response['pagination'] = [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'next_page_url' => $data->nextPageUrl(),
                'prev_page_url' => $data->previousPageUrl(),
            ];
        }

        return response()->json($response, $code);
    }

    /**
     * Return an error JSON response.
     */
    public static function Error($data, string $message, int $code = 500): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 0,
            'message' => $message,
            'data' => $data ?? (object)[],
        ], $code);
    }

    /**
     * Return a validation error JSON response.
     */
    public static function Validation($data, string $message, int $code = 422): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 0,
            'message' => $message,
            'errors' => $data ?? [],
        ], $code);
    }
}
