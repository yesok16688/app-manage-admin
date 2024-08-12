<?php

namespace App\Http\Controllers\Admin;

use App\Enum\AppStatus;
use App\Http\Controllers\Controller;

class OptionController extends Controller
{
    public function getRegionOptions()
    {
        $regions = config('common.region');
        return $this->jsonDataResponse($regions);
    }

    public function getChannelOptions()
    {
        $channels = config('common.channel');
        return $this->jsonDataResponse($channels);
    }

    public function getSubmitStatusOptions()
    {
        return $this->jsonDataResponse(AppStatus::desc());
    }
}
