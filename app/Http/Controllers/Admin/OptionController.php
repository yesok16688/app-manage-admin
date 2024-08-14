<?php

namespace App\Http\Controllers\Admin;

use App\Enum\AppStatus;
use App\Http\Controllers\Controller;
use App\Models\RedirectUrl;

class OptionController extends Controller
{
    public function getOptions()
    {
        $options = [
            'common' => [
                'region' => config('common.region'),
                'channel' => config('common.channel')
            ],
            'app' => [
                'submitStatus' => AppStatus::desc(),
                'groupCodes' => $this->getRedirectGroupCodes(),
            ]
        ];
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
}
