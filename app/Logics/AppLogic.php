<?php

namespace App\Logics;

use App\Enum\AppStatus;
use App\Models\AppVersion;

class AppLogic
{
    public function getLatestVersionByAppId($appId)
    {
        return AppVersion::query()
            ->where('app_id', $appId)
            ->where('status', AppStatus::PUBLISH->value)
            ->orderByDesc('version')
            ->first(['version', 'upgrade_mode', 'download_link']);
    }
}
