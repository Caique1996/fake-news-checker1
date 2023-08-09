<?php

namespace App\Observers;

use App\Libs\GoogleSearchResultObject;
use App\Models\GoogleSearchResult;
use App\Models\Search;

class SearchObserver
{
    /**
     * Handle the Search "created" event.
     *
     * @param \App\Models\Search $search
     * @return void
     */
    public function created(Search $search)
    {
        $google = new \App\Libs\GoogleCustomSearch();
        if (!is_null($search->search_term) && strlen($search->search_term) >= 3) {
            $results = $google->search($search->search_term);
            foreach ($results as $result) {
                $resultObject = new GoogleSearchResultObject($result);
                $model = new GoogleSearchResult();
                $model->search_id = $search->id;
                $model->title = $resultObject->getTitle();
                $model->url = $resultObject->getUrl();
                $model->description = $resultObject->getDescription();
                $model->image = $resultObject->getImage();
                $model->date_published = $resultObject->getDate();
                $model->json = json_encode($result);
                $model->save();
            }
        }


    }


}
