<?php

namespace App\Libs;


use Illuminate\Support\Facades\Http;

class GoogleCustomSearch
{
    public function search(string $query): array
    {
        $params = [
            'cx' => env("CUSTOM_SEARCH_ID"),
            'key' => env("CUSTOM_SEARCH_API"),
            'q' => $query
        ];

        $searchs = Http::get("https://customsearch.googleapis.com/customsearch/v1", $params)->json();
        if (isset($searchs['searchInformation']['totalResults']) && $searchs['searchInformation']['totalResults'] == 0) {
            if (isset($searchs['spelling']['correctedQuery'])) {
                $query = $searchs['spelling']['correctedQuery'];
                $params = [
                    'cx' => env("CUSTOM_SEARCH_ID"),
                    'key' => env("CUSTOM_SEARCH_API"),
                    'q' => $query
                ];
                $searchs = Http::get("https://customsearch.googleapis.com/customsearch/v1", $params)->json();
            }
        }
        if (!isset($searchs['items'])) {
            $searchs['items'] = [];
        }
        return $searchs['items'];
    }
}
