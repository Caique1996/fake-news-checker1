<?php

namespace App\Http\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NewsStoreRequest;
use App\Services\NewsSearchService;
use App\Services\ApiService;
use App\Services\SearchService;

class NewsController extends Controller
{
    public $user;
    public $requestData;
    public $searchService;

    public function __construct()
    {
        $this->user = getApiUser();
        $this->requestData = request()->all();
        $this->searchService = new SearchService();
    }

    public function store()
    {
        $requestData = $this->requestData;
        $rules = (new NewsStoreRequest())->rules();
        $service = new NewsSearchService();
        $searchService = $this->searchService;
        $callback = function () use ($requestData, $service, $searchService) {
            $news = $service->createOrGet($requestData['url']);
            $search = $news->getSearch();
            return $searchService->formatApResponse($search);
        };
        return ApiService::processApiRequest($rules, $requestData, $callback);
    }

}
