<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Api extends Model
{
    use CrudTrait;
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'user_id',
        'token',
        'ip_whitelist',
        'status',
        'request_limit',
        'webhook_url'
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function getAllowedIps()
    {
        return explodeComma($this->ip_whitelist);
    }

    public function customLinks()
    {
        $links = [
            [
                'label' => __('Requests'),
                'link' => '',
                'icon' => '',
                'class' => '',
            ]
        ];
        return $links;
    }


}
