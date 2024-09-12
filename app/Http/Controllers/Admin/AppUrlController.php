<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\CodeException;
use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\AppUrl;
use App\Utils\CryptUtils\RsaUtil;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenSSLAsymmetricKey;

class AppUrlController extends Controller
{
    private RsaUtil $rsaUtil;
    private OpenSSLAsymmetricKey $encryptKey;


    public function __construct()
    {
        $this->rsaUtil = new RsaUtil();
        $encryptKeyPath = config('auth.encrypt_key_path');
        $this->encryptKey = openssl_pkey_get_private(file_get_contents($encryptKeyPath));
    }

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
            ->when(!is_null($request->get('is_in_used')), function(Builder $query) {
                $query->where('is_in_used', request()->get('is_in_used'));
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
            'url' => 'required',
            'check_url' => 'required_if:type,1',
            'is_enable' => 'required|in:0,1',
            'is_in_used' => 'required|in:0,1',
            'is_reserved' => 'required_if:type,0|in:0,1',
            'remark' => '',
        ]);

        // 判断url是否RSA2048加密串，是则直接保存，不是则校验URL格式
        if(strlen($data['url']) !== 344) {
            $encryptData = $this->rsaUtil->encrypt($data['url'], $this->encryptKey);
            $request->validate(['url' => 'url']);
            $data['url'] = $encryptData;
        }
        if($data['check_url'] && strlen($data['check_url']) !== 344) {
            $encryptData = $this->rsaUtil->encrypt($data['check_url'], $this->encryptKey);
            $request->validate(['check_url' => 'url']);
            $data['check_url'] = $encryptData;
        }
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
            'url' => 'required',
            'check_url' => 'required_if:type,1',
            'is_enable' => 'in:0,1',
            'is_in_used' => 'in:0,1',
            'is_reserved' => 'required_if:type,0|in:0,1',
            'remark' => '',
        ]);
        // 判断url是否RSA2048加密串，是则直接保存，不是则校验URL格式
        if(strlen($data['url']) !== 344) {
            $encryptData = $this->rsaUtil->encrypt($data['url'], $this->encryptKey);
            $request->validate(['url' => 'url']);
            $data['url'] = $encryptData;
        }
        if($data['check_url'] && strlen($data['check_url']) !== 344) {
            $encryptData = $this->rsaUtil->encrypt($data['check_url'], $this->encryptKey);
            $request->validate(['check_url' => 'url']);
            $data['check_url'] = $encryptData;
        }
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
