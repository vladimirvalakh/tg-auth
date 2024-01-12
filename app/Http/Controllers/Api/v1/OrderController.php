<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Models\Order;
use App\Models\Tow;
use App\Models\City;
use App\Models\Site;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\OrderService;
use Illuminate\Routing\Controller as BaseController;

class OrderController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/api/v1/order/send",
     *     summary="Send Order",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         request="Site ID",
     *         description="Site ID",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="site_id",
     *                     description="Site ID",
     *                     type="string",
     *                     maximum=64,
     *                     example="31"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         request="Site ID",
     *         description="Site ID",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="site_id",
     *                     description="Site ID",
     *                     type="string",
     *                     maximum=64,
     *                     example="31"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(
     *                  property="data",
     *                  type="string",
     *                  example="All ok!",
     *              ),
     *          )
     *     ),
     *     @OA\Response(response=400, description="Bad data was send"),
     *     @OA\Response(response=500, description="internal server error")
     * )
     *
     * @param Request $request
     * @param OrderService $orderService
     * @return JsonResponse
     */
    public function send(Request $request, OrderService $orderService): JsonResponse
    {
        $data = $request->all();

        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Отсутствует токен авторизации'
            ], 403);
        }

        if ($token !== env('API_KEY')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Доступ запрещён! Токен авторизации не корректный'
            ], 403);
        }

        if (empty($data['site_id'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Отсутствует поле site_id'
            ]);
        }

        if (empty($data['phone'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Отсутствует поле phone'
            ]);
        }

        if (empty($data['tow_id'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Отсутствует поле tow_id'
            ]);
        }

        if (empty($data['source'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Отсутствует поле source'
            ]);
        }

        $tow = Tow::getTowByTowID($data['tow_id']);

        if (!$tow) {
            return response()->json([
                'status' => 'error',
                'message' => 'нет такого кода типа работ'
            ]);
        }

        $request['city_id'] = Site::getCityId($data['site_id']);
        $userId = Site::getArendatorId($data['site_id']);

        $request['user_id'] = $userId;

        $sendNotification = $orderService->sendOrderToArendator($request->toArray());

        if ($sendNotification == "User is missing") {
            return response()->json([
                'status' => 'error',
                'message' => 'К сайту не привязан арендатор, не удалось отправить уведомление'
            ]);
        }

        if ($sendNotification == "User not found") {
            return response()->json([
                'status' => 'error',
                'message' => 'Арендатор с таким id не найден, не удалось отправить уведомление'
            ]);
        }

        if ($sendNotification == "Site not found") {
            return response()->json([
                'status' => 'error',
                'message' => 'Сайт с таким id не найден'
            ]);
        }

        if ($sendNotification == "Email not found") {
            return response()->json([
                'status' => 'error',
                'message' => 'Email не найден'
            ]);
        }

        if ($sendNotification == "Tow not found") {
            return response()->json([
                'status' => 'error',
                'message' => 'Тип работ с таким tow_id не найден'
            ]);
        }

        if ($sendNotification == "ok") {
            return response()->json([
                'status' => 'success',
                'message' => 'Всё прошло успешно! Уведомление отправлено'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => json_encode($request->all())
        ]);
    }
}
