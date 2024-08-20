<?php

namespace App\Http\Controllers\App;

use App\Enum\UrlHandleStatus;
use App\Exceptions\ApiCallException;
use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\RedirectUrl;
use App\Models\RegionBlacklist;
use App\Models\UrlHandleLog;
use App\Utils\IPUtils\IPUtil;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppController extends Controller
{
    private ?Model $appInfo;

    public function __construct(Request $request)
    {
        $apiKey = $request->json('api_key');
        $this->appInfo = App::query()->where('api_key', $apiKey)->first();
    }

    /**
     * @throws ApiCallException
     */
    public function init(): JsonResponse
    {
        $status = 1;
        $info = [
            'status' => $status,
            'redirect_urls' => [],
        ];
        $appInfo = $this->appInfo;
        if(empty($this->appInfo)) {
            return $this->jsonDataResponse($info);
        }
        $enableRedirect = $this->checkRedirect($appInfo);
        $redirectUrls = [];
        if($enableRedirect) {
            $status = 99;
            $redirectUrls = RedirectUrl::query()
                ->where('group_code', $appInfo->redirect_group_code)
                ->where('is_enable', 1)
                ->where('type', 1)
                ->get(['id', 'url', 'check_url'])
                ->toArray();
        }

        $info['status'] = $status;
        $info['redirect_urls'] = $redirectUrls;
        return $this->jsonDataResponse($info);
    }

    /**
     * 刷新A链接
     * @param Request $request
     * @return JsonResponse
     * @throws ApiCallException
     */
    public function refresh(Request $request): JsonResponse
    {
        $data = $request->validate([
            'invalid_list' => 'array',
            'invalid_list.*.id' => 'integer|exists:redirect_urls,id',
            'invalid_list.*.http_status' => 'integer',
            'invalid_list.*.remark' => ''
        ], [
            'invalid_list.*.id' => 'url is not exists',
        ]);

        // 保存上报记录
        $clientIP = $request->getClientIp();
        try {
            $ipLocation = IPUtil::getLocation($clientIP);
        } catch (ApiCallException $exception) {
            $ipLocation = null;
        }
        $invalidList = $data['invalid_list'];
        $logs = [];
        foreach($invalidList as $invalidItem) {
            $logs[] = [
                'url_id' => $invalidItem['id'],
                'http_status' => $invalidItem['http_status'] ?? 0,
                'status' => UrlHandleStatus::CREATED->value,
                'client_ip' => $clientIP,
                'client_ip_region' => $ipLocation ? $ipLocation->getCountryCode() : '',
                'client_ip_sub_region' => $ipLocation ? $ipLocation->getRegionCode() : '',
            ];
        }
        if($logs) {
            UrlHandleLog::insert($logs);
        }

        $redirectUrls = RedirectUrl::query()
            ->where('group_code', $this->appInfo->redirect_group_code)
            ->where('is_enable', 1)
            ->where('type', 0)
            ->get(['id', 'url', 'is_reserved'])
            ->toArray();

        $info = [
            'api_urls' => [],
            'api_reserved_urls' => []
        ];
        foreach($redirectUrls as $redirectUrl) {
            $item = [
                'id' => $redirectUrl['id'],
                'url' => $redirectUrl['url']
            ];
            if($redirectUrl['is_reserved'] == 0) {
                $info['api_urls'][] = $item;
            } else {
                $info['api_reserved_urls'][] = $item;
            }
        }
        return $this->jsonDataResponse($info);
    }

    /**
     * 刷新B链接
     * @param Request $request
     * @return JsonResponse
     */
    public function tag(Request $request): JsonResponse
    {
        $data = $request->validate([
            'invalid_list' => 'array',
            'invalid_list.*.id' => 'integer|exists:redirect_urls,id',
            'invalid_list.*.http_status' => 'integer',
            'invalid_list.*.remark' => ''
        ], [
            'invalid_list.*.id' => 'url is not exists',
        ]);

        // 保存上报记录
        $clientIP = $request->getClientIp();
        try {
            $ipLocation = IPUtil::getLocation($clientIP);
        } catch (ApiCallException $exception) {
            $ipLocation = null;
        }
        $invalidList = $data['invalid_list'];
        $logs = [];
        foreach($invalidList as $invalidItem) {
            $logs[] = [
                'url_id' => $invalidItem['id'],
                'http_status' => $invalidItem['http_status'] ?? 0,
                'status' => UrlHandleStatus::CREATED->value,
                'client_ip' => $clientIP,
                'client_ip_region' => $ipLocation ? $ipLocation->getCountryCode() : '',
                'client_ip_sub_region' => $ipLocation ? $ipLocation->getRegionCode() : '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        if($logs) {
            UrlHandleLog::insert($logs);
        }
        return $this->jsonResponse();
    }

    /**
     * @throws ApiCallException
     */
    private function checkRedirect(?App $appInfo):bool
    {
        if(!$appInfo) {
            return false;
        }
        if($appInfo->enable_redirect == 0) {
            return false;
        }
        if(!$this->validateIPLocation(request()->ip())) {
            return false;
        }
        return true;
    }

    /**
     * @throws ApiCallException
     */
    private function validateIPLocation(string $ip): bool
    {
        //$ip = '172.105.62.113';
        // 印度非禁区
        $ip = '103.116.26.17';
        // 印度禁区
        // $ip = '103.107.37.148';
        // 越南
        //$ip = '14.178.106.226';
        // 马来
        //$ip = '175.141.26.50';
        Log::info('validating ip:' . $ip . '; app mange region:' . $this->appInfo->region);
        $ipLocation = IPUtil::getLocation($ip);
        Log::info('validating ip:' . $ip . '; location=' . $ipLocation->getCountryCode() . ':' . $ipLocation->getRegionCode() . '(' . $ipLocation->getRegionName() . ')');
        $blacklist = RegionBlacklist::query()
            ->where('region_code', $this->appInfo->region)
            ->where('is_enable', 1)
            ->get(['type', 'region_code', 'sub_region_codes'])
            ->keyBy('type')
            ->sortKeys()
            ->toArray();
        if(!$blacklist) {
            return true;
        }
        $blackResult = true;
        $whiteResult = true;
        foreach($blacklist as $type => $item) {
            $regionCode = $item['region_code'];
            $subRegionCodes = $item['sub_region_codes'];
            // 校验黑名单
            // 1. 子地区为空则表示整个地区都列入黑名单
            // 2. 子地区不为空则校验子地区是否包含
            if($type == 0 && $regionCode == $ipLocation->getCountryCode() &&
                (empty($subRegionCodes) || in_array($ipLocation->getRegionCode(), $subRegionCodes))) {
                Log::info('validating ip: match blacklist');
                $blackResult = false;
            }
            // 校验白名单
            // 1. 子地区为空则整个地区都列入白名单
            // 2. 子地区不为空则校验子地区是否包含
            if($type == 1 && ($regionCode != $ipLocation->getCountryCode() ||
                    (!empty($subRegionCodes) && !in_array($ipLocation->getRegionCode(), $subRegionCodes)))) {
                Log::info('validating ip: not in whitelist');
                $whiteResult = false;
            }
        }
        return $blackResult && $whiteResult;
    }
}
