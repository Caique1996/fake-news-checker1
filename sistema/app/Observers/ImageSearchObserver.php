<?php

namespace App\Observers;

use App\Enums\SearchType;
use App\Models\ImageSearch;
use App\Models\Search;
use Illuminate\Support\Str;

class ImageSearchObserver
{

    public function created(ImageSearch $row)
    {
        $search = new Search();
        $search->search_term = $row->extracted_text;
        $search->object_id = $row->id;
        $search->type = SearchType::Image;
        $search->ip = getUserIp();
        $search->saveOrFail();
    }


}
