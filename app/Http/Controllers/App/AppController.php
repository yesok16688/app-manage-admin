<?php

namespace App\Http\Controllers\App;

use App\Exceptions\ApiCallException;
use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\RedirectUrl;
use App\Models\RegionBlacklist;
use App\Utils\IPUtils\IPUtil;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppController extends Controller
{
    private Model $appInfo;

    /**
     * @throws ApiCallException
     */
    public function init(Request $request): JsonResponse
    {
        $status = 1;
        $apiKey = $request->header('x-api-key');
        $this->appInfo = $appInfo = App::query()->where('api_key', $apiKey)->first();
        $enableRedirect = $this->checkRedirect($appInfo);
        $redirectUrls = [];
        if($enableRedirect) {
            $status = 99;
            $redirectUrls = RedirectUrl::query()
                ->where('group_code', $appInfo->redirect_group_code)
                ->where('is_enable', 1)
                ->orderBy('order')
                ->limit(5)
                ->get()
                ->pluck('url');
        }

        $info = [
            'status' => $status,
            'redirect_url' => $redirectUrls,
            'name' => '',   // 迷惑字段
            'language' => 'us', // 迷惑字段
            'config' => [], // 迷惑字段
        ];
        return $this->jsonDataResponse($info);
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
        // $ip = '103.116.26.17';
        // 印度禁区
        // $ip = '103.107.37.148';
        // 越南
        $ip = '14.178.106.226';
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
