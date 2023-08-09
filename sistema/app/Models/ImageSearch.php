<?php

namespace App\Models;

use App\Enums\SearchType;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImageSearch extends Model
{

    use CrudTrait;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'id', 'image', 'extracted_text', 'checksum', 'created_at', 'updated_at'
    ];

    public function getFormatedImageLink()
    {
        return getImageLink($this->image);
    }

    public function getHtmlImage()
    {
        return imageWithHash($this->image, $this->checksum);
    }

    public function showDownloadBtn()
    {
        $link = route("image-search.download", ['checksum' => $this->checksum]);
        return customHtmlLink($link, 'la la-arrow-circle-down text-success', __("Download"));
    }

    public function getSearch()
    {
        return Search::whereType(SearchType::Image)->whereObjectId($this->id)->first();
    }

}
