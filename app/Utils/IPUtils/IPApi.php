<?php

namespace App\Utils\IPUtils;

use App\Exceptions\ApiCallException;
use Illuminate\Support\Facades\Http;

/**
 * link: https://ip-api.com/docs/api:json
 */
class IPApi
{
    private string $urlPattern = 'http://ip-api.com/json/%s';

    /**
     * @throws ApiCallException
     */
    public function getLocation(string $ip): IPLocateInfo
    {
        $url = sprintf($this->urlPattern, $ip);
        $responseRaw = Http::asJson()->get($url);
        if(empty($responseRaw)) {
            throw new ApiCallException($url, [], 'blank response');
        }
        $response = json_decode($responseRaw, true);
        if($response['status'] === 'fail') {
            throw new ApiCallException($url, [], $responseRaw);
        }
        $info = new IPLocateInfo();
        $info->setIp($ip);
        $info->setCountryCode($response['countryCode'] ?: '');
        $info->setRegionCode($response['region'] ?: '');
        $info->setRegionName($response['regionName'] ?: '');
        $info->setCity($response['city'] ?: '');
        return $info;
    }
}
