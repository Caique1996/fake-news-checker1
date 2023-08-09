<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GoogleSearchResult extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = ['id', 'search_id', 'title', 'url', 'description','site', 'image', 'date_published', 'json', 'created_at', 'updated_at'];

    public function search(): HasOne
    {
        return $this->hasOne(Search::class, 'id', 'search_id');
    }

    public function getTitle()
    {
        return html_ahref($this->title, $this->url, true);
    }

    public function getUrl()
    {
        return newInputWithCopyBtn($this->url, 'new_btn_' . $this->id);
    }


}
