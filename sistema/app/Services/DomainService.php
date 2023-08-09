<?php

namespace App\Services;

use App\Libs\WebScraping;
use App\Models\Domain;
use Ramsey\Uuid\Uuid;
use Spatie\SslCertificate\SslCertificate;

class DomainService
{


    public function createOrUpdateDomainData(string $domain)
    {
        $domain = clearUrl($domain);
        $domainData = Domain::whereDomain($domain)->first();
        $data = [];
        if (isset($domainData['id'])) {
            $data['id'] = $domainData['id'];
        }
        $whoisService = new WhoisService();
        $domainData = $whoisService->getDomainInfo($domain);
        if (!isset($domainData['domain_registered']) || $domainData['domain_registered'] != 'yes') {
            return false;
        }


        $metaData = new WebScraping('http://' . $domain);
        $service = new DomainReputationService();
        $reputation = $service->getDomainReputation($domain);
        if (!isset($reputation['risk_score']['result'])) {
            $riskCore = 0;
        } else {
            $riskCore = $reputation['risk_score']['result'];
        }
        $data['domain'] = $domain;
        $data['title'] = $metaData->getSiteTitle();
        $data['description'] = $metaData->getSiteDescription();

        if (isset($domainData['registrant_contact']['name'])) {
            $data['registrant_name'] = $domainData['registrant_contact']['name'];
        } else {
            $data['registrant_name'] = "";
        }


        if (isset($domainData['registrant_contact']['company'])) {
            $data['registrant_company'] = $domainData['registrant_contact']['company'];
        } else {
            $data['registrant_company'] = "";
        }

        $data['register_date'] = $domainData['create_date'];
        $data['risk_score'] = $riskCore;
        $certificate = SslCertificate::createForHostName($domain);
        $data['json'] = json_encode([
            'whois' => $domainData,
            'reputation' => $reputation,
            'ssl' => ['is_valid' => $certificate->isValid(),
                'expiration_date' => $certificate->expirationDate(),
                'issuer' => $certificate->getIssuer()]
        ]);
        if (isset($data['id'])) {
            $item = Domain::whereId($data['id'])->update($data);
        } else {
            $item = Domain::create($data);
        }
        return $item;
    }

}
