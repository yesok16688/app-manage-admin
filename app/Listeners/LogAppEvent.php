<?php

namespace App\Listeners;

use App\Events\AppReported;
use App\Models\AppEventLog;
use App\Utils\IPUtils\IPUtil;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogAppEvent implements ShouldQueue
{
    public $connection = 'redis';
    public $queue = 'app-event';

    public $tries = 1;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AppReported $event): void
    {
        $IPLocation = $event->IPLocation ?? IPUtil::getLocation($event->clientIp);
        $appEvent = new AppEventLog();
        $appEvent->app_version_id = $event->appVersionId;
        $appEvent->event_code = $event->eventCode;
        $appEvent->sub_event_code = $event->subEventCode;
        $appEvent->client_ip = $event->clientIp;
        $appEvent->client_ip_region_code = $IPLocation ? $IPLocation->getCountryCode() : '';
        $appEvent->client_ip_sub_region_code = $IPLocation ? $IPLocation->getRegionCode() : '';
        $appEvent->device_id = $event->deviceId;
        $appEvent->lang_code = $event->langCode;
        $appEvent->domain = $event->domain;
        $appEvent->remark = $event->msg;
        $appEvent->save();
    }
}
