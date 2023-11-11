<?php

namespace App\Http\Controllers\Api\v1;

use Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="API",
 *      description="API documentation",
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

//    public function __construct(UserRepository $userRepository) {
//        if (Auth::check()) {
//            $userRepository->updateLastVisit();
//        }
//    }
}
