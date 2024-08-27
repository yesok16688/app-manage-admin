<?php

namespace App\Utils\IPUtils;

use App\Exceptions\ApiCallException;
use Exception;
use GeoIp2\Exception\AddressNotFoundException;
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
}
