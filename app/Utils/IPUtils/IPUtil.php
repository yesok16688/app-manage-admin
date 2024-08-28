<?php

namespace App\Utils\IPUtils;

use App\Exceptions\ApiCallException;
use Exception;
use GeoIp2\Exception\AddressNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IPUtil
{
    /**
     * @throws ApiCallException
     */
    public static function getLocation(string $ip): ?IPLocateInfo
    {
        try {
            return (new GeoIP2())->getLocation($ip);
        }
        catch (AddressNotFoundException $e) {
            return null;
        } catch (Exception $e) {
            Log::error('GeoIP2 error: ' . $e->getMessage());
        }
        return (new IPApi())->getLocation($ip);
    }

    public static function getCFLocation(Request $request): ?IPLocateInfo
    {
        $clientIP = request()->header('CF-Connecting-IP');
        $country = request()->header('CF-IPCountry');
        if(!$clientIP || !$country) {
            return null;
        }
        $city = request()->header('CF-IPCity'); // 需要 Enterprise 版
        $region = request()->header('CF-IPRegion'); // 需要 Enterprise 版
        $info = new IPLocateInfo();
        $info->setIp($clientIP);
        $info->setCountryCode($country);
        $info->setRegionCode($region);
        $info->setCity($city);
        return $info;
    }
}
