<?php

namespace App\Models;

use App\Enums\BoolStatus;
use App\Enums\UserType;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use CrudTrait;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id', 'name', 'email', 'type', 'email_verified_at', 'password', 'document', 'remember_token', 'status', 'created_at', 'updated_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    protected $appends = ['beautiful_name'];

    public function getBeautifulNameAttribute()
    {
        return "{$this->name} - {$this->email}";
    }

    public function getTranslatedType()
    {
        return __($this->type);
    }

    public function getPwd()
    {
        return "";
    }

    public function apis()
    {
        return $this->hasMany(Api::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }


    public function createDefaultApi()
    {
        $api = $this->apis()->first();
        if (!isset($api['id'])) {
            $data = [
                'status' => BoolStatus::Active,
                'ip_whitelist' => '127.0.0.1',
                'request_limit' => (int)MetaData::getValue('default_rate_limit')
            ];
            return $this->apis()->create($data);
        }

        return $api;

    }

    public function hasActiveApi()
    {
        $api = $this->createDefaultApi();
        if (isset($api['id'])) {
            return true;
        }
        return false;
    }

    public function hasPermissionWithThisUser($userId, $operation)
    {

        $where = getWhereConditionUsers($this, $operation, 'id');
        $count = User::where('id', $userId)->where($where)->count();
        if ($count >= 1) {
            return true;
        }
        return false;
    }

    public function getAllPerms()
    {
        if ($this->type == UserType::Moderator) {
            $json = \Storage::disk('local')->get('mod_perms.json');
            return json_decode($json, true);
        }
        if ($this->type == UserType::Subscriber) {
            $json = \Storage::disk('local')->get('sub_perms.json');
            return json_decode($json, true);
        }
    }

    public function hasPermission($perm)
    {
        registerPermission($perm);

        if ($this->type == UserType::Admin) {
            return true;
        } else {
            $permissions = $this->getAllPerms();
            if (in_array($perm, $permissions)) {
                return true;
            }
        }


        return false;
    }

    public function isSuperUser(): bool
    {

        return $this->isAdmin();
    }

    public static function adminsIds()
    {
        $ids = [];
        $users = self::where("type", UserType::Admin)->get();
        foreach ($users as $user) {
            $ids[$user->id] = $user->id;
        }
        return $ids;
    }

    public function isAdmin(): bool
    {
        if ($this->type == UserType::Admin) {
            return true;
        }
        return false;
    }

}
