<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class ApiController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/v1/healthcheck",
     *     summary="Validate a server status",
     *     tags={"System"},
     *
     *     @OA\Response(response=200, description="Server is valid"),
     * )
     *
     * Responds to requests to GET api/v1/healthcheck
     **
     * @return JsonResponse
     */
    public function healthcheck(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
        ]);
    }
}
