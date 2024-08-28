<?php

namespace App\Http\Controllers\Admin;

use App\Enum\AppStatus;
use App\Exceptions\CodeException;
use App\Exceptions\ErrorCode;
use App\Http\Controllers\Controller;
use App\Models\AppFile;
use App\Models\AppVersion;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            ->with(['icon', 'imgs'])
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
            ->orderByDesc('id')
            ->paginate($request->get('per_page'), ['*'], 'page', $request->get('current_page'));
        return $this->jsonDataResponse($list);
    }

    /**
     * Store a newly created resource in storage.
     * @throws \Throwable
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'app_id' => 'required|exists:apps,id',
            'app_name' => 'required',
            'api_key' => 'required',
            'version' => 'required|regex:/^\d{1,3}\.\d{1,3}\.\d{1,3}$/',
            'icon_id' => 'exists:files,id',
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

        try {
            DB::beginTransaction();

            $appVersion = new AppVersion($request->input());
            $appVersion->save();
            $appImages = array_map(function($imgId) use($appVersion) {
                return new AppFile([
                    'version_id' => $appVersion->id,
                    'file_id' => $imgId
                ]);
            }, $request->input('app_img_ids'));
            $appImages && $appVersion->imgs()->saveMany($appImages);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $this->jsonResponse();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $info = AppVersion::query()->with(['icon', 'imgs'])->findOrFail($id);
        return $this->jsonResponse($info);
    }

    /**
     * Update the specified resource in storage.
     * @throws CodeException
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'app_name' => '',
            'icon_id' => 'exists:files,id',
            'version' => '',
            'download_link' => 'url',
            'status' => 'in:' . join(',', AppStatus::values()),
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
            'app_img_ids.*' => 'exists:files,id'
        ]);
        $appVersion = AppVersion::query()->findOrFail($id);
        if(isset($data['version']) && ($appVersion['status'] != AppStatus::UN_SUBMIT->value) && ($appVersion['version'] !== $data['version'])) {
            throw new CodeException('version must not be modified', ErrorCode::INVALID_PARAMS);
        }
        try {
            DB::beginTransaction();

            $appVersion->update($data);
            $appImages = array_map(function($imgId) use($appVersion) {
                return new AppFile([
                    'version_id' => $appVersion->id,
                    'file_id' => $imgId
                ]);
            }, $request->input('app_img_ids'));
            $appVersion->imgs()->detach();
            $appVersion->imgs()->saveMany($appImages);

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $this->jsonResponse();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $info = AppVersion::query()->findOrFail($id);
        $info->delete();
        return $this->jsonResponse();
    }
}
