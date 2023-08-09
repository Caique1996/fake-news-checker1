<?php

namespace App\Http\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImageSearchStoreRequest;
use App\Services\ImageSearchService;
use App\Services\ApiService;
use App\Services\SearchService;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public $user;
    public $requestData;
    public $searchService;

    public function __construct()
    {
        $this->user = getApiUser();
        $this->requestData = request()->all();
        $this->request = request();
        $this->searchService = new SearchService();
    }

    public function store(Request $request)
    {
        $requestData = $this->requestData;
        $rules = (new ImageSearchStoreRequest())->rules();
        $searchService = $this->searchService;
        $request = $this->request;

        $callback = function () use ($requestData, $request, $searchService) {
            $service = new ImageSearchService();
            $image = $service->process($request);

            return $searchService->formatApResponse($image->getSearch());
        };
        return ApiService::processApiRequest($rules, $requestData, $callback);
    }

}
