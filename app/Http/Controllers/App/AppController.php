<?php

namespace app\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\RedirectUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function init(Request $request): JsonResponse
    {
        $status = 1;
        $apiKey = $request->header('x-api-key');
        $appInfo = App::query()->where('api_key', $apiKey)->first();
        $enableRedirect = $this->checkRedirect($appInfo);
        $redirectUrls = [];
        if($enableRedirect) {
            $status = 99;
            $redirectUrls = RedirectUrl::query()
                ->where('group_code', $appInfo->redirect_group_code)
                ->where('is_enable', 1)
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

    private function checkRedirect(?App $appInfo):bool
    {
        if(!$appInfo) {
            return false;
        }
        if($appInfo->enable_redirect == 0) {
            return false;
        }

        return true;
    }
}
