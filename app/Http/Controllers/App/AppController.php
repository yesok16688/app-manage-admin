<?php

namespace App\Http\Controllers\App;

use App\Enum\AppStatus;
use App\Enum\EventCode;
use App\Enum\UpgradeMode;
use App\Enum\UrlHandleStatus;
use App\Events\AppReported;
use App\Events\BadUrlReported;
use App\Exceptions\ApiCallException;
use App\Http\Controllers\Controller;
use App\Logics\AppLogic;
use App\Models\AppUrl;
use App\Models\UrlHandleLog;
use App\Utils\IPUtils\IPLocateInfo;
use App\Utils\IPUtils\IPUtil;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PHPUnit\Event\Event;

class AppController extends Controller
{
    private $appInfo;
    private $redirectCheckMsg;

    /**
     * @throws ApiCallException
     */
    public function init(Request $request): JsonResponse
    {
        $appInfo = $this->appInfo = $request->input('app_info');
        $status = 1;
        $info = [
            'status' => $status,
            'redirect_urls' => [],
            'latest_version' => '0.0.0',
            'upgrade_mode' => UpgradeMode::UPGRADE_MODE_IGNORE->value,
            'app_url' => '',
        ];
        if(empty($appInfo)) {
            return $this->jsonDataResponse($info);
        }
        $latestVersion = app(AppLogic::class)->getLatestVersionByAppId($appInfo['app']['id']);
        if($latestVersion) {
            $info['latest_version'] = $latestVersion['version'] ?? '0.0.0';
            $info['upgrade_mode'] = $latestVersion['upgrade_mode'] ?? UpgradeMode::UPGRADE_MODE_IGNORE->value;
            $info['app_url'] = $latestVersion['download_link'] ?? '';
        }
        $enableRedirect = $this->checkRedirect($appInfo);
        $redirectUrls = [];
        $clientIP = $this->getClientIp();
        AppReported::dispatch($appInfo['id'], EventCode::OA->value, '', $clientIP,
            request()->input('device_id', ''), request()->input('lang_code', ''),
            request()->getHost(), $this->redirectCheckMsg, $this->getIPLocation($clientIP));
        if($enableRedirect) {
            $status = 99;   // 与客户端约定的开启跳转的状态码
            $redirectUrls = AppUrl::query()
                ->where('app_id', $appInfo['app_id'])
                ->where('is_enable', 1)
                ->where('type', AppUrl::TYPE_B)
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
     */
    public function refresh(Request $request): JsonResponse
    {
        $appInfo = $request->input('app_info');
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
            $ipLocation = $this->getIPLocation($clientIP);
        } catch (ApiCallException $exception) {
            $ipLocation = null;
        }
        $invalidList = $data['invalid_list'];
        BadUrlReported::dispatch($appInfo['id'], $invalidList, $ipLocation);

        $appInfo = $request->input('app_info');
        $apiUrls = AppUrl::query()
            ->where('app_id', $appInfo['app']['id'])
            ->where('is_enable', 1)
            ->where('type', AppUrl::TYPE_A)
            ->get(['id', 'url', 'is_reserved'])
            ->toArray();

        $info = [
            'api_urls' => [],
            'api_reserved_urls' => []
        ];
        foreach($apiUrls as $apiUrl) {
            $item = [
                'id' => $apiUrl['id'],
                'url' => $apiUrl['url']
            ];
            if($apiUrl['is_reserved'] == 0) {
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
        $appInfo = $request->input('app_info');

        // 保存上报记录
        $clientIP = $request->getClientIp();
        try {
            $ipLocation = $this->getIPLocation($clientIP);
        } catch (ApiCallException $exception) {
            $ipLocation = null;
        }
        $invalidList = $data['invalid_list'];
        BadUrlReported::dispatch($appInfo['id'], $invalidList, $ipLocation);
        return $this->jsonResponse();
    }

    private function getClientIp(): string
    {
        $clientIP = request()->header('CF-Connecting-IP');
        if(!$clientIP) {
            $clientIP = request()->ip();
        }
        return $clientIP;
    }

    /**
     * @throws ApiCallException
     */
    private function checkRedirect($appInfo):bool
    {

        $clientIP = $this->getClientIp();
        // IP白名单可以直接打开跳转
        if($appInfo['ip_whitelist'] && in_array($clientIP, explode(',', $appInfo['ip_whitelist']))) {
            $this->redirectCheckMsg = '[true]ip whitelist';
            return true;
        }
        // 强制不跳转
        if($appInfo['disable_jump']) {
            $this->redirectCheckMsg = '[false]disable_jump';
            return false;
        }
        // 审核中强制不跳转
        if($appInfo['status'] == AppStatus::CHECKING->value) {
            $this->redirectCheckMsg = '[false]app checking';
            return false;
        }
        // 手机语言黑名单
        $langCode = request()->input('lang_code');
        if(!$langCode || ($appInfo['lang_blacklist'] && in_array($langCode, $appInfo['lang_blacklist']))) {
            $this->redirectCheckMsg = '[false]client lang=' . $langCode . ' or in lang_blacklist';
            return false;
        }
        $deviceId = request()->input('device_id');
        if(!$deviceId || ($appInfo['device_blacklist'] && in_array($deviceId, explode(',', $appInfo['device_blacklist'])))) {
            $this->redirectCheckMsg = '[false]client device=' . $langCode . ' or in device_blacklist';
            return false;
        }

        // 检测IP限制
        if(!$this->validateIPLocation($clientIP)) {
            return false;
        }
        return true;
    }

    /**
     * @throws ApiCallException
     */
    private function getIPLocation($ip): ?IPLocateInfo
    {
        $ipLocation = IPUtil::getCFLocation(request());
        if(!$ipLocation) {
            $ipLocation = IPUtil::getLocation($ip);
        }
        return $ipLocation;
    }

    /**
     * @throws ApiCallException
     */
    private function validateIPLocation(string $ip): bool
    {

        // $ip = '172.105.62.113';
        // 印度非禁区
        // $ip = '103.116.26.17';
        // 印度禁区
        // $ip = '103.107.37.148';
        // 越南
        //$ip = '14.178.106.226';
        // 马来
        //$ip = '175.141.26.50';

        // 检查IP黑名单
        if ($this->appInfo['ip_blacklist'] && in_array($ip, explode(',', $this->appInfo['ip_blacklist']))) {
            $this->redirectCheckMsg = '[false]ip_blacklist';
            return false;
        }

        // 检查是否需要判断IP仅限上架地区访问
        if(!$this->appInfo['is_region_limit']) {
            $this->redirectCheckMsg = '[true]region no limit';
            return true;
        }

        //Log::info('validating ip:' . $ip . '; app mange region:' . $this->appInfo->region);
        $ipLocation = $this->getIPLocation($ip);
        if(!$ipLocation) {
            //Log::info('validating ip:' . $ip . '; ip not found');
            $this->redirectCheckMsg = '[false]ip=' . $ip . '; region not found';
            return false;
        }
        //Log::info('validating ip:' . $ip . '; location=' . $ipLocation->getCountryCode() . ':' . $ipLocation->getRegionCode() . '(' . $ipLocation->getRegionName() . ')');

        // 校验IP是否在上架地区列表中
        $regionCodes = $this->appInfo['app']['region_codes'];
        if($regionCodes && in_array($ipLocation->getCountryCode(), $regionCodes)) {
            $this->redirectCheckMsg = '[true]region limit';
            return true;
        }
        $this->redirectCheckMsg = '[false]region limit';
        return false;
//        $blacklist = RegionBlacklist::query()
//            ->where('region_code', $this->appInfo->region)
//            ->where('is_enable', 1)
//            ->get(['type', 'region_code', 'sub_region_codes'])
//            ->keyBy('type')
//            ->sortKeys()
//            ->toArray();
//        if(!$blacklist) {
//            return true;
//        }
//        $blackResult = true;
//        $whiteResult = true;
//        foreach($blacklist as $type => $item) {
//            $regionCode = $item['region_code'];
//            $subRegionCodes = $item['sub_region_codes'];
//            // 校验黑名单
//            // 1. 子地区为空则表示整个地区都列入黑名单
//            // 2. 子地区不为空则校验子地区是否包含
//            if($type == 0 && $regionCode == $ipLocation->getCountryCode() &&
//                (empty($subRegionCodes) || in_array($ipLocation->getRegionCode(), $subRegionCodes))) {
//                Log::info('validating ip: match blacklist');
//                $blackResult = false;
//            }
//            // 校验白名单
//            // 1. 子地区为空则整个地区都列入白名单
//            // 2. 子地区不为空则校验子地区是否包含
//            if($type == 1 && ($regionCode != $ipLocation->getCountryCode() ||
//                    (!empty($subRegionCodes) && !in_array($ipLocation->getRegionCode(), $subRegionCodes)))) {
//                Log::info('validating ip: not in whitelist');
//                $whiteResult = false;
//            }
//        }
//        return $blackResult && $whiteResult;
    }
}
