<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
