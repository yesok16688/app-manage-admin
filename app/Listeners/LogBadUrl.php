<?php

namespace App\Listeners;

use App\Enum\UrlHandleStatus;
use App\Events\BadUrlReported;
use App\Models\UrlHandleLog;
use App\Models\UrlHandleLogDetail;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogBadUrl implements ShouldQueue
{
    public $connection = 'redis';
    public $queue = 'url-reported';

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
    public function handle(BadUrlReported $event): void
    {
        foreach($event->invalidList as $invalidItem) {
            $log = UrlHandleLog::query()->where('url_id', $invalidItem['id'])->orderByDesc('id')->first();
            // URL_ID对应URL信息不存在直接忽略不处理
            if($log && empty($log['url'])) {
                continue;
            }
            // URL已经处于禁用状态则不再记录
            if($log && $log['status'] != UrlHandleStatus::CREATED->value && $log['url']['is_enable'] == 0) {
                continue;
            }
            // 首次记录
            if(empty($log)) {
                $log = new UrlHandleLog();
                $log->url_id = $invalidItem['id'];
                $log->status = UrlHandleStatus::CREATED->value;
                $log->save();

                $detail = new UrlHandleLogDetail();
                $detail->url_handle_log_id = $log->id;
                $detail->app_version_id = $event->versionId;
                $detail->url_id = $invalidItem['id'];
                $detail->http_status = $invalidItem['http_status'] ?? 0;
                $detail->client_ip = $event->ipLocation ? $event->ipLocation->getIp() : '';
                $detail->client_ip_region = $event->ipLocation ? $event->ipLocation->getCountryCode() : '';
                $detail->client_ip_sub_region = $event->ipLocation ? $event->ipLocation->getRegionCode() : '';
                $detail->save();
                continue;
            }

            $detail = UrlHandleLogDetail::query()->where('url_handle_log_id', $log->id)
                ->where('client_ip', $event->ipLocation ? $event->ipLocation->getIp() : '')
                ->first();
            if($detail) {
                continue;
            }
            $detail = new UrlHandleLogDetail();
            $detail->url_handle_log_id = $log->id;
            $detail->app_version_id = $event->versionId;
            $detail->url_id = $invalidItem['id'];
            $detail->http_status = $invalidItem['http_status'] ?? 0;
            $detail->client_ip = $event->ipLocation ? $event->ipLocation->getIp() : '';
            $detail->client_ip_region = $event->ipLocation ? $event->ipLocation->getCountryCode() : '';
            $detail->client_ip_sub_region = $event->ipLocation ? $event->ipLocation->getRegionCode() : '';
            $detail->save();
        }
    }
}
