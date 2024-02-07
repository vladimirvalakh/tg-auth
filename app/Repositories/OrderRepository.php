<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Order;
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
            ->select('orders.id as order_id', 'orders.phone as orders_phone','sites.url as site_url', 'orders.status as orders_status')
            ->where('orders.order_status', '=', $status)
            ->get();
    }
}
