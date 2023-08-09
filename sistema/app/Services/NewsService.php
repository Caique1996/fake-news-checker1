<?php

namespace App\Services;

use App\Enums\SearchType;
use App\Http\Requests\Admin\NewsStoreRequest;
use App\Models\Search;

class NewsService
{
    function store()
    {
        $postData = $this->requestData;
        $user = $this->user;
        $rules = (new NewsStoreRequest())->rules();

        $callback = function () use ($postData, $user) {

            $newsSearch = new \App\Services\NewsSearchService();
            return $newsSearch->createOrGet($postData['url']);

        };
        return ApiService::processApiRequest($rules, $postData, $callback);
    }



}
