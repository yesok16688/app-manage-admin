<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\CodeException;
use App\Exceptions\ErrorCode;
use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\AppVersion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $list = App::query()
            ->with(['latestVersion', 'aUrls', 'bUrls'])
            ->when($request->get('name'), function(Builder $query, $name) {
                $query->where('name', 'like', '%' . $name . '%');
            })
            ->when($request->get('region_code'), function(Builder $query, $regionCode) {
                $query->whereRaw('FIND_IN_SET(\'' . $regionCode . '\', region_codes)');
            })
            ->when($request->get('channel'), function(Builder $query, $value) {
                $query->where('channel', $value);
            })
            ->paginate($request->get('per_page'), ['*'], 'page', $request->get('current_page'))
            ->toArray();

        foreach ($list['data'] as &$item) {
            $aLinkNormals = array_filter($item['a_urls'], function($item) {
                return $item['is_enable'] == 1 && $item['is_reserved'] == 0 && $item['is_in_used'] == 1;
            });
            $aLinkNotInUsed = array_filter($item['a_urls'], function($item) {
                return $item['is_enable'] == 1 && $item['is_in_used'] == 0;
            });
            $aLinkSpares = array_filter($item['a_urls'], function($item) {
                return $item['is_enable'] == 1 && $item['is_reserved'] == 1 && $item['is_in_used'] == 1;
            });
            $aLinkAbnormals = array_filter($item['a_urls'], function($item) {
                return $item['is_enable'] == 0;
            });
            $bLinkNormals = array_filter($item['b_urls'], function($item) {
                return $item['is_enable'] == 1 && $item['is_reserved'] == 0 && $item['is_in_used'] == 1;
            });
            $bLinkNotInUsed = array_filter($item['b_urls'], function($item) {
                return $item['is_enable'] == 1 && $item['is_in_used'] == 0;
            });
            $bLinkSpares = array_filter($item['b_urls'], function($item) {
                return $item['is_enable'] == 1 && $item['is_reserved'] == 1 && $item['is_in_used'] == 1;
            });
            $bLinkAbnormals = array_filter($item['b_urls'], function($item) {
                return $item['is_enable'] == 0;
            });
            $item['a_link_info'] = [
                'normal' => count($aLinkNormals),
                'spare' => count($aLinkSpares),
                'abnormal' => count($aLinkAbnormals),
                'not_used' => count($aLinkNotInUsed)
            ];
            $item['b_link_info'] = [
                'normal' => count($bLinkNormals),
                'spare' => count($bLinkSpares),
                'abnormal' => count($bLinkAbnormals),
                'not_used' => count($bLinkNotInUsed)
            ];
        }
        return $this->jsonDataResponse($list);
    }

    /**
     * Store a newly created resource in storage.
     * @throws \Throwable
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required',
            'region_codes' => 'required|array',
            'region_codes.*' => 'required|exists:regions,iso_code',
            'channel' => 'required|in:' . join(',', array_keys(config('common.channel'))),
            'remark' => '',
        ]);
        App::create($data);
        return $this->jsonResponse();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $info = App::query()->findOrFail($id);
        return $this->jsonResponse($info);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'name' => '',
            'region_codes' => 'array',
            'region_codes.*' => 'required|exists:regions,iso_code',
            'channel' => 'in:' . join(',', array_keys(config('common.channel'))),
            'remark' => '',
        ]);
        $info = App::query()->findOrFail($id);
        $info->update($data);
        return $this->jsonResponse();
    }

    /**
     * Remove the specified resource from storage.
     * @throws CodeException
     */
    public function destroy(string $id): JsonResponse
    {
        $versionInfo = AppVersion::query()->where('app_id', $id)->first(['id']);
        if($versionInfo) {
            throw new CodeException('应用存在版本数据，不能删除', ErrorCode::INVALID_PARAMS);
        }
        $info = App::query()->findOrFail($id);
        $info->delete();
        return $this->jsonResponse();
    }
}
