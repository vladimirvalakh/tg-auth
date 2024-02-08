<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Order;
use App\Models\Rent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderRepository extends CustomRepository
{
    public function store(array $data): Order
    {
        return Order::create($data);
    }

    public function getOrdersByStatus(string $status): ?Collection
    {
        return DB::table('orders')
            ->join('sites', 'orders.site_id', '=', 'sites.id')
            ->select('orders.id as order_id', 'orders.source as order_source', 'orders.info as order_info', 'orders.phone as order_phone','sites.url as site_url', 'orders.status as orders_status')
            ->where('orders.order_status', '=', $status)
            ->get();
    }

    public function update(array $data, Model $record, $files = []): bool
    {
        return parent::update($data, $record, $files);
    }

    public function approve($orderId) {
        $order = Order::find($orderId);

        $order['order_status'] = Order::ON_RENT_STATUS;
        $order['rental_period_up_to'] = Carbon::create($order['date'])->addMonth();
        $order->save();

        $rent = Rent::where('site_id', $order['site_id'])->first();
        $rent['status'] = Rent::ON_RENT_STATUS;
        $rent['user_id'] = $order['user_id'];
        $rent['emails'] = $order['emails'];
        $rent->save();
    }

    public function decline($orderId, string $reason = "", string $comment = "") {
        $order = Order::find($orderId);

        $commentText = "";

        if ($reason !== "") {
            $commentText = $reason;
        }

        if ($comment !== "" && $reason !== "") {
            $commentText .= ", ";
        }

        if ($comment !== "") {
            $commentText .= $comment;
        }

        if ($commentText !== "") {
            $order['comm_moderator'] = $commentText;
        }
        $order['order_status'] = Order::ORDER_STATUS_DECLINED;
        $order->save();

        $rent = Rent::where('site_id', $order['site_id'])->first();
        $rent['status'] = Rent::IN_SEARCH_STATUS;
        $rent->save();
    }

    public function getOrderIdByRentId($rentId): ?int
    {
        $rent = Rent::find($rentId);
        $order = Order::where('user_id', $rent->user_id)->where('site_id', $rent->site_id)->first();
        return ($order) ? $order->id : null;
    }
}
