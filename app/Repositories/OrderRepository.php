<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Order;

class OrderRepository extends CustomRepository
{
    public function store(array $data): Order
    {
        return Order::create($data);
    }
}
