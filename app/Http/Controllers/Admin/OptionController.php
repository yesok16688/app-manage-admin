<?php

namespace App\Http\Controllers\Admin;

use App\Enum\AppStatus;
use App\Enum\UrlHandleStatus;
use App\Http\Controllers\Controller;
use App\Models\RedirectUrl;
use App\Models\Region;
use App\Models\SubRegion;

class OptionController extends Controller
{
    public function getOptions()
    {
        $options = [
            'common' => [
                'region' => $this->getRegions(),
                'channel' => config('common.channel')
            ],
            'app' => [
                'submitStatus' => AppStatus::desc(),
                'groupCodes' => $this->getRedirectGroupCodes(),
                'handleStatus' => UrlHandleStatus::desc(),
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

    private function getRedirectGroupCodes()
    {
        $groupCodes = RedirectUrl::query()
            ->where('is_enable', 1)
            ->pluck('group_code', 'group_code')
            ->toArray();
        return $groupCodes;
    }

    private function getRegions()
    {
        return Region::query()
            ->where('is_enable', 1)
            ->pluck('name_cn', 'iso_code')
            ->toArray();
    }
}
