<?php

namespace App\Http\Controllers\Admin;

use App\Enum\AppStatus;
use App\Http\Controllers\Controller;
use App\Models\AppVersion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppVersionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'app_id' => 'required',
        ]);
        $list = AppVersion::query()
            ->with('imgs')
            ->where('app_id', $request->input('app_id'))
            ->when($request->get('app_name'), function(Builder $query, $name) {
                $query->where('app_name', 'like', '%' . $name . '%');
            })
            ->when($request->get('version'), function(Builder $query, $value) {
                $query->where('version', 'like', '%' . $value . '%');
            })
            ->when($request->get('status'), function(Builder $query, $value) {
                $query->where('status', $value);
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
        if(!$request->input('icon_id')) {
            $request->offsetUnset('icon_id');
        }
        $request->validate([
            'app_id' => 'required|exists:apps,id',
            'app_name' => 'required',
            'api_key' => 'required',
            'version' => 'required|regex:/^\d{1,3}\.\d{1,3}\.\d{1,3}$/',
            'icon_id' => 'nullable|exists:files,id',
            'download_link' => 'url',
            'status' => 'required|in:' . join(',', AppStatus::values()),
            'is_region_limit' => 'required|in:0,1',
            'ip_blacklist' => 'array',
            'ip_blacklist.*' => 'required|ipv4',
            'ip_whitelist' => 'array',
            'ip_whitelist.*' => 'required|ipv4',
            'lang_blacklist' => 'array',
            'lang_blacklist.*' => 'required|exists:langs,lang_code',
            'disable_jump' => 'required|in:0,1',
            'upgrade_mode' => 'required|in:0,1,2',
            'app_img_ids' => 'array',
            'app_img_ids.*' => 'exists:files,id'
        ], [
            'version.regex' => '版本号只能是“数字.数字.数字”格式'
        ]);
        $appImages = $request->input('app_img_ids');
        $appVersion = new AppVersion($request->input());
        $appVersion->save();
        $appVersion->imgs()->saveMany($appImages);
        return $this->jsonResponse();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $info = AppVersion::query()->with('imgs')->findOrFail($id);
        return $this->jsonResponse($info);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'app_id' => 'required|exists:apps,id',
            'app_name' => '',
            'icon_id' => 'in:files,id',
            'download_link' => 'url',
            'status' => 'in:' . join(',', AppStatus::keys()),
            'is_region_limit' => 'in:0,1',
            'ip_blacklist' => 'array',
            'ip_blacklist.*' => 'ipv4',
            'ip_whitelist' => 'array',
            'ip_whitelist.*' => 'ipv4',
            'lang_blacklist' => 'array',
            'lang_blacklist.*' => 'exists:langs,lang_code',
            'disable_jump' => 'in:0,1',
            'upgrade_mode' => 'in:0,1,2',
            'app_img_ids' => 'array',
            'app_img_ids.*' => 'exits:files,id'
        ]);
        $appVersion = AppVersion::query()->findOrFail($id);
        $appVersion->update($data);
        $appVersion->imgs()->saveMany($data['app_img_ids']);
        return $this->jsonResponse();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $info = AppVersion::query()->findOrFail($id);
        $info->delete();
        return $this->jsonResponse();
    }
}
