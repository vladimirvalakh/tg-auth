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

    public function getUserIdByTelegramUsername(string $telegramUsername): int
    {
        return User::where('username', $telegramUsername)->value('id');
    }

    public function getUserIdByTelegramId($telegramId): int
    {
        return User::where('telegram_id', $telegramId)->value('id');
    }

    public function getUserByTelegramId($telegramId)
    {
        return User::where('telegram_id', $telegramId)->first();
    }
}
