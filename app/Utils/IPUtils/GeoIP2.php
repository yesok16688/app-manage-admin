<?php

namespace App\Utils\IPUtils;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use LogicException;
use MaxMind\Db\Reader\InvalidDatabaseException;

/**
 * link: https://dev.maxmind.com/geoip/docs/web-services?lang=en
 */
class GeoIP2
{
    private Reader $reader;

    /**
     * @throws InvalidDatabaseException
     */
    public function __construct()
    {
        $mmdb = config('geoip2.mmdb_path');
        if(empty($mmdb)) {
            throw new LogicException('config:geoip2.mmdb_path is not defined');
        }
        $this->reader = new Reader($mmdb);
    }

    /**
     * @param string $ip
     * @return IPLocateInfo
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    public function getLocation(string $ip): IPLocateInfo
    {
        $response = $this->reader->enterprise($ip);
        $info = new IPLocateInfo();
        $info->setIp($ip);
        $info->setCountryCode($response->country->isoCode);
        $info->setRegionCode('');
        if(isset($response->subdivisions[0]) && $response->subdivisions[0]->isoCode) {
            $info->setRegionCode($response->subdivisions[0]->isoCode);
        }
        $info->setRegionName(isset($response->subdivisions[0]) ? $response->subdivisions[0]->names['en'] : '');
        $info->setCity($response->city->names['en']);
        return $info;
    }
}
