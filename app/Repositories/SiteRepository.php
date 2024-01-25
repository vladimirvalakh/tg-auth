<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Site;
use App\Models\Rent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SiteRepository extends CustomRepository
{
    public function getSite($siteId): ?Site
    {
        return Site::find($siteId);
    }

    public function getAddedSitesOfUserId($userId): ?Collection
    {
        return DB::table('sites')
            ->join('rents', 'rents.site_id', '=', 'sites.id')
            ->select('sites.id','sites.url')
            ->where('rents.user_id', '=', $userId)
            ->where('rents.status', '=', Rent::ON_RENT_STATUS)
            ->get();
    }
}
