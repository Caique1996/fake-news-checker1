<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class HtmlFile extends Model
{
    use HasFactory;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['url', 'html'];

    public static function createAndGetHtmlFile($url, $domainMode = true)
    {
        if ($domainMode) {
            $cleanUrl = getHostFromUrl($url);
        } else {
            $cleanUrl = clearUrl($url);
        }

        $data = self::where("url", $cleanUrl)->orderBy("id", "desc")->first();
        if (isset($data['id'])) {
            if ($data->hasExpired()) {
                $html = Http::get($url)->getBody();
                $data = ['url' => $cleanUrl, 'html' => $html];
                $created = self::create($data);
                return $created['html'];
            }
            return $data['html'];
        } else {
            $html = Http::get($url)->getBody();
            $data = ['url' => $cleanUrl, 'html' => $html];
            $created = self::create($data);
            $data = self::where("url", $cleanUrl)->orderBy("id", "desc")->first();

            return $data['html'];
        }
    }

    public function hasExpired()
    {

        if (is_null($this->created_at)) {
            return true;
        }
        $expireDate = strtotime($this->created_at . " +7 days");
        $today = strtotime(date("Y-m-d H:i:s"));
        if ($expireDate <= $today) {
            return true;
        }
        return false;
    }
}
