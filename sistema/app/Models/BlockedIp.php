<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockedIp extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'ip_address',
        'reason',
        'expires_at'
    ];

    /**
     * @var string[]
     */
    protected $dates = [

        'created_at',
        'updated_at',
    ];

    public function scopeValid($query)
    {
        $now = time();
        return $query->whereRaw("UNIX_TIMESTAMP(expires_at)>=$now");
    }

    public static function isBlocked($ip = null)
    {
        if (is_null($ip)) {
            $ip = getUserIp();
        }
        $row = self::where('ip_address', $ip)->valid()->first();
        if (isset($row['id'])) {
            return $row;
        } else {
            return false;
        }
    }

}
