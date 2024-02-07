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
            ->select('sites.id','sites.url', 'rents.status')
            ->where('rents.user_id', '=', $userId)
            ->where('rents.status', '=', Rent::ON_RENT_STATUS)
            ->get();
    }

    public function getSitesByRentStatus(string $status): ?Collection
    {
        return DB::table('rents')
            ->join('sites', 'rents.site_id', '=', 'sites.id')
            ->select('rents.id as rent_id', 'rents.phone as rent_phone','sites.url as site_url', 'rents.status as rent_status', 'sites.last_month_orders_count as site_last_month_orders_count')
            ->where('rents.status', '=', $status)
            ->get();
    }
}
