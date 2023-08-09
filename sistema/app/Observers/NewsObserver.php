<?php

namespace App\Observers;

use App\Enums\SearchType;
use App\Models\ImageSearch;
use App\Models\News;
use App\Models\Search;
use Illuminate\Support\Str;

class NewsObserver
{

    public function created(News $row)
    {
        $search = new Search();
        $search->search_term = $row->title;
        $search->object_id = $row->id;
        $search->type = SearchType::News;
        $search->ip = getUserIp();
        $search->saveOrFail();
    }


}
