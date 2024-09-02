<?php

namespace App\Http\Controllers\App;

use App\Enum\EventCode;
use App\Enum\SubEventCode;
use App\Events\AppReported;
use App\Http\Controllers\Controller;
use App\Models\AppEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    private $appInfo;

    public function report(Request $request): JsonResponse
    {
        $appInfo = $this->appInfo = $request->input('app_info');
        $data = $request->validate([
            'event_code' => 'required|in:' . join(',', EventCode::values()),
            'sub_event_code' => 'in:' . join(',', SubEventCode::values()),
            'device_id' => '',
            'lang_code' => ''
        ]);
        AppReported::dispatch();
        return $this->jsonDataResponse($appInfo);
    }
}
