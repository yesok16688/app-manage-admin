<?php

namespace App\Utils\IPUtils;

class IPLocateInfo
{
    private string $countryCode;
    private string $regionCode;
    private string $regionName;
    private string $city;
    private string $ip;

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    public function getRegionCode(): string
    {
        return $this->regionCode;
    }

    public function setRegionCode(?string $regionCode): void
    {
        $this->regionCode = $regionCode ?? '';
    }

    public function getRegionName(): string
    {
        return $this->regionName ?? '';
    }

    public function setRegionName(?string $regionName): void
    {
        $this->regionName = $regionName ?? '';
    }

    public function getCity(): string
    {
        return $this->city ?? '';
    }

    public function setCity(?string $city): void
    {
        $this->city = $city ?? '';
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }
}
