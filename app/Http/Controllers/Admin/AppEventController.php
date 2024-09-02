<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppEventLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppEventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $list = AppEventLog::query()
            ->with(['version.app'])
            ->when($request->get('event_code'), function(Builder $query, $value) {
                $query->where('event_code', $value);
            })
            ->when($request->get('sub_event_code'), function(Builder $query, $value) {
                $query->where('sub_event_code', $value);
            })
            ->when($request->get('client_ip'), function(Builder $query, $value) {
                $query->where('client_ip', $value);
            })
            ->when($request->get('device_id'), function(Builder $query, $value) {
                $query->where('device_id', $value);
            })
            ->when($request->get('lang_code'), function(Builder $query, $value) {
                $query->where('lang_code', $value);
            })
            ->when($request->get('domain'), function(Builder $query, $value) {
                $query->where('domain', $value);
            })
            ->when($request->get('client_ip_region_code'), function(Builder $query, $value) {
                $query->where('client_ip_region_code', $value);
            })
            ->when($request->get('created_at'), function(Builder $query, $value) {
                $query->where('created_at', '>=',  $value[0])
                    ->where('created_at', '<=', $value[1]);
            })
            ->when($request->get('remark'), function(Builder $query, $value) {
                $query->where('remark', 'like', "%$value%");
            })
            ->orderByDesc('id')
            ->paginate($request->get('per_page'), ['*'], 'page', $request->get('current_page'))
            ->toArray();
        return $this->jsonDataResponse($list);
    }
}
