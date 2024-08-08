<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function jsonResponse($msg = 'SUCCESS', array $data = null): JsonResponse
    {
        if($data) {
            return response()->json(['code' => 0, 'msg' => $msg, 'data' => $data]);
        }
        return response()->json(['code' => 0, 'msg' => $msg]);
    }

    public function jsonDataResponse(array $data = null): JsonResponse
    {
        return response()->json(['code' => 0, 'msg' => 'SUCCESS', 'data' => $data]);
    }
}
