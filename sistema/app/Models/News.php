<?php

namespace App\Models;

use App\Enums\BoolStatus;
use App\Enums\SearchType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use CrudTrait;
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id', 'title', 'description', 'domain', 'url', 'image', 'json', 'created_at', 'updated_at'
    ];

    public function getTitle()
    {
        return html_ahref($this->title, $this->url, true);
    }

    public function getShortTitle()
    {
        return newInputWithCopyBtn($this->url, 'new_btn_' . $this->id);

    }

    public function getUrl()
    {
        return;;
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getSearch()
    {
        return Search::whereType(SearchType::News)->whereObjectId($this->id)->first();
    }

    public function getDomainModel()
    {
        return Domain::whereDomain($this->domain)->first();
    }

    public function isHumorNews()
    {
        $humorSites = HumorSite::whereStatus(BoolStatus::Active)->get();
        $link = $this->url;
        foreach ($humorSites as $site) {
            if (str_contains($link, $site->site)) {
                return true;
            }
        }
        return false;
    }

}
