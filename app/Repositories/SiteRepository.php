<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Site;

class SiteRepository extends CustomRepository
{
    public function getSite($siteId): ?Site
    {
        return Site::find($siteId);
    }
}
