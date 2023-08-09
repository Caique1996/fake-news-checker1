<?php

namespace App\Services;

use App\Libs\WebScraping;
use App\Models\ImageSearch;
use App\Models\News;

class NewsSearchService
{
    public function createOrGet(string $url): ?News
    {
        $domainParse = parse_url($url);
        if (!isset($domainParse['host'])) {
            throw new  \Exception(__("Invalid url."));
        }
        $url = getCleanNewsUrl($url);
        $row = News::whereUrl($url)->first();
        if (!isset($row['id'])) {
            $domain = $domainParse['host'];
            $domainService = new DomainService();
            $domainService->createOrUpdateDomainData($domain);
            $metaData = new WebScraping($url, false);
            $newsModel = new News();
            $newsModel->title = $metaData->getSiteTitle();
            $newsModel->description = $metaData->getSiteDescription();
            $newsModel->domain = $domain;
            $newsModel->image = $metaData->getImage();
            $newsModel->url = $url;
            $newsModel->saveOrFail();
            $row = $newsModel;
        }

        return $row;
    }
}
