<?php

namespace App\Models;

use App\Enums\SearchType;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SearchWithObject extends Search
{
    use CrudTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'searches_with_objects';
    protected $fillable = ['id', 'object_id', 'type', 'ip', 'search_term', 'object_data', 'qty_reviews', 'checksum'];

    public function getTerm()
    {
        $object = $this->getObjectModel();
        if (is_null($object)) {
            return null;
        }
        if ($this->type == SearchType::News) {
            return $object->getTitle();
        }

        return $this->search_term;
    }

    public function getObjectData()
    {
        $object = $this->getObjectModel();
        if (is_null($object)) {
            return null;
        }
        if ($this->type == SearchType::News) {
            return $object->getShortTitle();
        }
        if ($this->type == SearchType::Image) {
            return $object->getHtmlImage();
        }
        return null;
    }
    public function getExternalResults()
    {

        return getExternalResultsLink($this->id);
    }

}
