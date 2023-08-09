<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exception extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'ip',
        'file',
        'message',
        'trace',
        'extra_data'
    ];

    public static function registerAndAbort($ex, $userId = null)
    {
        self::register($ex, $userId);
        abort(500);
    }

    public static function register($ex, $userId = null)
    {
        try {
            $file = $ex->getFile() . ":" . $ex->getLine();
            $message = $ex->getMessage();
            $trace = $ex->getTraceAsString();


            $ip = getUserIp();

            if (is_null($userId)) {
                $user = getUserActor();
                if (isset($user['id'])) {
                    $userId = $user['id'];
                }
            }


            $data = [
                'user_id' => $userId,
                'ip' => $ip,
                'file' => $file,
                'message' => $message,
                'trace' => $trace,
            ];
            if (property_exists($ex, 'extraData')) {
                $data['extra_data'] = $ex->getExtraDataAsJson();
            }

            $record = Exception::create($data);
            if (isLocalEnv()) {
                return $message . '|' . $file;
            } else {
                return __("An internal server error has occurred. Try again. Error code: :code", ['code' => $record->id]);
            }
        } catch (\Exception $e) {

            return __("An internal server error has occurred. Try again. Error code: :code", ['code' => '']);
        }

    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
