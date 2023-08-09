<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Google\Cloud\WebRisk\V1\ThreatType;
use Google\Cloud\WebRisk\V1\WebRiskServiceClient;

class WebRiskService
{
    public function getDomainInfo(string $domain): array
    {
        $webrisk = new WebRiskServiceClient([
            'credentials' => base_path(env("GOOGLE_JSON_DIR"))
        ]);


        $response = $webrisk->searchUris($domain, [
            ThreatType::MALWARE,
            ThreatType::SOCIAL_ENGINEERING,
            ThreatType::UNWANTED_SOFTWARE
        ]);


        $threats = $response->getThreat();
        $response = [];
        if ($threats) {
            foreach ($threats->getThreatTypes() as $threat) {
                $response[] = ThreatType::name($threat);
            }
        }
        return $response;


    }
}
