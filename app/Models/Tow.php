<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class Tow
 *
 * @package App\Models
 * @property int $id
 * @property string $tow
 */
class Tow extends Model
{
    protected $table = 'tow';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tow',
    ];

    public static function getTowIdByName(string $tow)
    {
        return Tow::where('tow', $tow)->value('id');
    }

    public static function getTowByTowID(string $towId)
    {
        return Tow::where('id', $towId)->value('tow');
    }

    public static function towList(bool $multiple = true): ?array
    {
        $tows = DB::table('tow')->orderBy('tow', 'ASC')->pluck('tow', 'id')->toArray();

        if ($multiple) {
            array_unshift($tows, 'Выбрать всё');
        }

        return $tows;
    }
}
