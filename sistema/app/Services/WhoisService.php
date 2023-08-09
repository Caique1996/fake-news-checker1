<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhoisService
{
    public function getDomainInfo(string $domain)
    {
        $url="https://api.whoisfreaks.com/v1.0/whois";
        $params=[
            'apiKey'=>env('WHOIS_API'),
            'whois'=>'live',
            'domainName'=>$domain
        ];
        return Http::get($url,$params)->json();
    }
}
