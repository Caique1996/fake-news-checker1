<?php

namespace App\Models;

use App\Enums\BoolStatus;
use App\Enums\SearchType;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Search extends Model
{
    use CrudTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'searches';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'object_id', 'type', 'ip', 'search_term'];

    public function getObjectModel()
    {
        if ($this->type == SearchType::News) {
            return News::whereId($this->object_id)->first();
        }
        if ($this->type == SearchType::Image) {
            return ImageSearch::whereId($this->object_id)->first();
        }
        return null;
    }

    public function getFormatedTermWithLink()
    {
        $objectModel = $this->getObjectModel();
        if (!is_null($objectModel)) {
            if ($this->type == SearchType::News) {
                $url = url(route("news.show", ['id' => $objectModel->id]));
                $randName = 'copy_btn_' . sortHashCod();
                return newInputWithCopyBtn($url, $randName);
            }
            if ($this->type == SearchType::Image) {
                $url = url(route("image-search.show", ['id' => $objectModel->id]));
                return html_ahref($objectModel->getHtmlImage(), $url);
            }
        }
        return null;
    }

    public function getObjectLink()
    {
        $objectModel = $this->getObjectModel();
        if (!is_null($objectModel)) {
            if ($this->type == SearchType::News) {
                return $objectModel->url;
            }
            if ($this->type == SearchType::Image) {
                return url('storage/uploads/' . $objectModel->image);
            }
        }
        return null;
    }

    public function getFormatedTerm()
    {
        $objectModel = $this->getObjectModel();
        if (!is_null($objectModel)) {
            if ($this->type == SearchType::News) {
                return $objectModel->getTitle();
            }
            if ($this->type == SearchType::Image) {
                return $objectModel->getHtmlImage();
            }
        }
        return null;
    }

    public function getSearchTerm()
    {
        return $this->search_term;
    }

    public function getType()
    {
        return __($this->type);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function googleSearchResults(): HasMany
    {
        return $this->hasMany(GoogleSearchResult::class);
    }

    public function checkBtn()
    {

        return checkNews($this->id);
    }

    public function getFormatedGoogleSearchResults()
    {
        $responseArr = [];
        $results = $this->googleSearchResults()->get();
        foreach ($results as $result) {
            $responseArr[] = [
                'title' => $result['title'],
                'url' => $result['url'],
                'description' => $result['description'],
                'image' => $result['image'],
                'created_at' => $result['created_at'],
            ];
        }
        return $responseArr;
    }


}
