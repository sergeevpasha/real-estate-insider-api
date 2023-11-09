<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    protected function jsonResponse(JsonResource|array $data, $message = '', $code = 200): JsonResponse
    {
        return response()->json([
            'message' => __($message),
            'data'    => $data,
        ], $code);
    }
}
