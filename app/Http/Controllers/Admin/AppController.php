<?php

namespace App\Http\Controllers\Admin;

use App\Enum\AppStatus;
use App\Http\Controllers\Controller;
use App\Models\App;
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
            ->when($request->get('name'), function(Builder $query, $name) {
                $query->where('name', 'like', '%' . $name . '%');
            })
            ->when($request->get('region'), function(Builder $query, $region) {
                $query->where('region', $region);
            })
            ->when($request->get('channel'), function(Builder $query, $value) {
                $query->where('channel', $value);
            })
            ->when($request->get('submit_status'), function(Builder $query, $value) {
                $query->where('submit_status', $value);
            })
            ->when($request->get('enable_redirect'), function(Builder $query, $value) {
                $query->where('enable_redirect', $value);
            })
            ->when($request->get('redirect_group_code'), function(Builder $query, $value) {
                $query->where('redirect_group_code', $value);
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
            'name' => 'required',
            'api_key' => 'required',
            'region' => 'required|exists:regions,iso_code',
            'channel' => 'required|in:' . join(',', array_keys(config('common.channel'))),
            'submit_status' => 'required|in:' . join(',', AppStatus::values()),
            'enable_redirect' => 'required|in:0,1',
            'redirect_group_code' => 'required_if:enable_redirect,1|exclude_unless:enable_redirect,1|exists:redirect_urls,group_code',
            'remark' => '',
        ]);
        App::create($data);
        return $this->jsonResponse();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $info = App::query()->findOrFail($id);
        return $this->jsonResponse($info);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'name' => '',
            'api_key' => '',
            'region' => '|exists:regions,iso_code',
            'channel' => 'in:' . join(',', array_keys(config('common.channel'))),
            'submit_status' => 'in:' . join(',', AppStatus::values()),
            'enable_redirect' => 'in:0,1',
            'redirect_group_code' => 'required_if:enable_redirect,1|exclude_unless:enable_redirect,1|exists:redirect_urls,group_code',
            'remark' => '',
        ]);
        $info = App::query()->findOrFail($id);
        $info->update($data);
        return $this->jsonResponse();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $info = App::query()->findOrFail($id);
        $info->delete();
        return $this->jsonResponse();
    }
}
