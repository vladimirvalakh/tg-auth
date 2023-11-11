<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;

class UserRepository extends CustomRepository
{
    public function getUser(int $userId): ?User
    {
        return User::find($userId);
    }

    public function store(array $data): Order
    {
        return User::create($data);
    }
}
