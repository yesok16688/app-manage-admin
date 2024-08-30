<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\CodeException;
use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\AppUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppUrlController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $list = AppUrl::query()
            ->with(['app'])
            ->where('app_id', $request->get('app_id'))
            ->where('type', $request->get('type'))
            ->when(!is_null($request->get('is_enable')), function(Builder $query) {
                $query->where('is_enable', request()->get('is_enable'));
            })
            ->when(!is_null($request->get('is_reserved')), function(Builder $query) {
                $query->where('is_reserved', request()->get('is_reserved'));
            })
            ->paginate($request->get('per_page'), ['*'], 'page', $request->get('current_page'));
        return $this->jsonDataResponse($list);
    }

    /**
     * Store a newly created resource in storage.
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'app_id' => 'required|exists:apps,id',
            'type' => 'required|in:0,1',
            'url' => 'required|url:http,https',
            'check_url' => 'required_if:type,1|url:http,https',
            'is_enable' => 'required|in:0,1',
            'is_reserved' => 'required|in:0,1',
            'remark' => '',
        ]);
        AppUrl::create($data);
        return $this->jsonResponse();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $info = AppUrl::query()->findOrFail($id);
        return $this->jsonResponse($info);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'url' => 'url:http,https',
            'check_url' => 'required_if:type,1|url:http,https',
            'is_enable' => 'in:0,1',
            'is_reserved' => 'in:0,1',
            'remark' => '',
        ]);
        $info = AppUrl::query()->findOrFail($id);
        $info->update($data);
        return $this->jsonResponse();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $info = AppUrl::query()->findOrFail($id);
        $appInfo = App::query()->where('id', $info->app_id)->first();
        if($appInfo) {
            throw new CodeException("此链接正在使用中，请先解除应用绑定再操作");
        }
        $info->delete();
        return $this->jsonResponse();
    }
}
