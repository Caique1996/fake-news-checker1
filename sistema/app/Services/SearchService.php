<?php

namespace App\Services;

use App\Enums\BoolStatus;
use App\Enums\SearchType;
use App\Models\Domain;
use App\Models\Search;

class SearchService
{
    public function formatApResponse(Search $search)
    {
        $response = [];
        if ($search->type == SearchType::News) {
            $news = $search->getObjectModel();
            $domain = $news->getDomainModel();
            if (isset($domain['id'])) {
                $response['domain_info'] = [
                    'domain' => $news['domain'],
                    'description' => $domain['description'],
                    'title' => $domain['title'],
                    'registrant_company' => $domain['registrant_company'],
                    'registrant_name' => $domain['registrant_name'],
                    'risk_score' => $domain['risk_score'],
                    'register_date' => $domain['register_date']
                ];
            }
            $response['news'] = [
                'title' => $news['title'],
                'description' => $news['description'],
                'url' => $news['url'],
                'image' => $news['image'],
                'is_humor' => $news->isHumorNews(),
                'created_at' => $news['created_at'],
                'updated_at' => $news['updated_at']
            ];
            if ($response['news']['is_humor'] === true) {
                return $response;
            }
        } elseif ($search->type == SearchType::Image) {
            $image = $search->getObjectModel();
            $response['image'] = [
                'image' => $image->getFormatedImageLink(),
                'extracted_text' => $image['extracted_text'],
                'checksum' => $image['checksum'],
                'created_at' => $image['created_at'],
                'updated_at' => $image['updated_at']
            ];
        }
        $response['checks'] = [];
        $reviews = $search->reviews()->where("status", BoolStatus::Active)->get();
        foreach ($reviews as $review) {
            $response['checks'][] = [
                'check_status' => $review['check_status'],
                'text' => $review['text'],
                'sources' => $review->getFormatedSources(),
                'created_at' => $review['created_at'],
                'updated_at' => $review['updated_at']
            ];
        }
        $response['related-fact-checking'] = $search->getFormatedGoogleSearchResults();
        return $response;
    }

}
