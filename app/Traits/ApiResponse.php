<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait ApiResponse
{
    public function responseOk($data)
    {
        return response()->json([
            'data' => $data
        ], Response::HTTP_OK);
    }

    public function responseCreated($data)
    {
        return response()->json([
            'data' => $data
        ], Response::HTTP_CREATED);
    }

    public function responseDeleted() {
        return response()->json(null, 204);
    }
}