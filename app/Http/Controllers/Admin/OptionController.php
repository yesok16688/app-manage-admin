<?php

namespace App\Http\Controllers\Admin;

use App\Enum\AppStatus;
use App\Enum\UpgradeMode;
use App\Enum\UrlHandleStatus;
use App\Http\Controllers\Controller;
use App\Models\AppUrl;
use App\Models\Lang;
use App\Models\Region;
use App\Models\SubRegion;

class OptionController extends Controller
{
    public function getOptions()
    {
        $options = [
            'common' => [
                'region' => $this->getRegions(),
                'channel' => config('common.channel'),
                'lang' => $this->getLangs(),
            ],
            'app' => [
                'submitStatus' => AppStatus::desc(),
                'handleStatus' => UrlHandleStatus::desc(),
                'upgradeMode' => UpgradeMode::desc(),
            ]
        ];
        return $this->jsonDataResponse($options);
    }

    public function getSubRegionOptions(String $regionCode)
    {
        $options = SubRegion::query()
            ->where('region_code', $regionCode)
            ->where('is_enable', 1)
            ->pluck('name_en', 'iso_code')
            ->toArray();
        return $this->jsonDataResponse($options);
    }

    private function getRegions()
    {
        return Region::query()
            ->where('is_enable', 1)
            ->pluck('name_cn', 'iso_code')
            ->toArray();
    }

    private function getLangs()
    {
        return Lang::query()
            ->pluck('name_cn', 'lang_code')
            ->toArray();
    }
}
