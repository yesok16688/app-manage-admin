<?php

namespace App\Http\Controllers\Admin;

use App\Enum\UrlHandleStatus;
use App\Exceptions\CodeException;
use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\RedirectUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RedirectUrlController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $list = RedirectUrl::query()
            ->where('type', $request->get('type'))
            ->when($request->get('group_code'), function(Builder $query, $value) {
                $query->where('group_code', $value);
            })
            ->when($request->get('url'), function(Builder $query, $value) {
                $query->where('url', 'like', "%$value%");
            })
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
            'type' => 'required|in:0,1',
            'group_code' => 'required|max:50',
            'url' => 'required|url:http,https',
            'check_url' => 'required_if:type,1|url:http,https',
            'is_enable' => 'required|in:0,1',
            'is_reserved' => 'required|in:0,1',
            'remark' => '',
        ]);
        RedirectUrl::create($data);
        return $this->jsonResponse();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $info = RedirectUrl::query()->findOrFail($id);
        return $this->jsonResponse($info);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'order' => 'integer',
            'group_code' => 'max:50',
            'url' => 'url:http,https',
            'check_url' => 'url:http,https',
            'is_enable' => 'in:0,1',
            'is_reserved' => 'in:0,1',
            'remark' => '',
        ]);
        $info = RedirectUrl::query()->findOrFail($id);
        $info->update($data);
        return $this->jsonResponse();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $info = RedirectUrl::query()->findOrFail($id);
        $appInfo = App::query()->where('redirect_group_code', $info->group_code)->first();
        if($appInfo) {
            throw new CodeException("分组代码：{$info->group_code} 正在使用中，请先解除应用绑定再操作");
        }
        $info->delete();
        return $this->jsonResponse();
    }
}
