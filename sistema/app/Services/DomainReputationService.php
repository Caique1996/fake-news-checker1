<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DomainReputationService
{
    public function getDomainReputation(string $domain)
    {
        $url = "https://endpoint.apivoid.com/domainbl/v1/pay-as-you-go/";
        $params = [
            'key' => env('DOMAIN_REPUTATION_API'),
            'host' => $domain
        ];
        $arr = Http::get($url, $params)->json();
        if (isset($arr['data']['report'])) {
            return $arr['data']['report'];
        }
        return null;
    }
}
