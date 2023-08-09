<?php

namespace App\Models;

use Http\Client\Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApiRequest extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'url',
        'user_id',
        'api_id',
        'ip',
        'data',
        'response',
        'time'
    ];
    /**
     * @var string[]
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public static function insertApiRequest($response)
    {
        try {
            $api = getApi();
            if (!isset($api['id'])) {
                $apiId = null;
                $userId = null;
            } else {
                $user = $api->user()->first();
                $userId = $user['id'];
                $apiId = $api['id'];
            }
            if (is_array($response['data']) && isset($response['data']['real_user_id'])) {
                $userId = $response['data']['real_user_id'];
                unset($response['data']['real_user_id']);
            }
            $requestData = [
                'url' => request()->fullUrl(),
                'user_id' => $userId,
                'api_id' => $apiId,
                'ip' => getUserIp(),
                'data' => json_encode(request()->all()),
                'response' => json_encode($response),
                'time' => time()
            ];
            ApiRequest::create($requestData);
        } catch (\Exception $ex) {
        }

    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function api(): BelongsTo
    {
        return $this->belongsTo(Api::class);
    }

    public function formatUrlRequest()
    {
        $link = $this->url;

        $cn = e($link);

        return html_ahref(str_replace(url('/'), '', $cn), $cn);

    }

    public function formatApiId()
    {
        $link = route('api.show', ['id' => $this->api_id]);
        $link = e($link);
        $api = $this->api()->first();
        $name = "#" . $this->api_id;
        if (isset($api['id'])) {
            $name .= " - " . $api['name'];
        }
        return html_ahref($name, $link);
    }
}
