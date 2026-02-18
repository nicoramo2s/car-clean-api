<?php

namespace App;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function successResponse($data, $message = '', $status = 200, $extras = []): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            ...$extras,
        ], $status);
    }

    protected function errorResponse($message = '', $errors = null, $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'error' => $errors,
        ], $status);
    }
}
