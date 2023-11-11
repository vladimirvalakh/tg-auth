<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Tow;

class TowRepository extends CustomRepository
{
    public function getTow($towId): ?Tow
    {
        return Tow::find($towId);
    }

    public function getTowValueById($towId): ?String
    {
        return Tow::find($towId)->value('tow');
    }
}
