<?php

namespace App\Http\Controllers\App;

use App\Exceptions\ApiCallException;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    private $appInfo;

    /**
     * @throws ApiCallException
     */
    public function init(Request $request): JsonResponse
    {
        $appInfo = $this->appInfo = $request->input('app_info');
        return $this->jsonDataResponse($appInfo);
    }
}
