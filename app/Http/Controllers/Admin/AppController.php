<?php

namespace App\Http\Controllers\Admin;

use App\Enum\AppStatus;
use App\Http\Controllers\Controller;
use App\Models\App;
use Illuminate\Http\Request;

class AppController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $list = App::query()
            ->paginate();
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
            'region' => 'required|in:' . join(',', array_keys(config('common.region'))),
            'channel' => 'required|in:' . join(',', array_keys(config('common.channel'))),
            'submit_status' => 'required|in:' . join(',', AppStatus::values()),
            'enable_redirect' => 'required|in:0,1',
            'redirect_group_code' => 'required_if:enable_redirect,1|exists:redirect_urls,group_code'
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
            'region' => 'in:' . join(',', array_keys(config('common.region'))),
            'channel' => 'in:' . join(',', array_keys(config('common.channel'))),
            'submit_status' => 'in:' . join(',', AppStatus::values()),
            'enable_redirect' => 'in:0,1',
            'redirect_group_code' => 'required_if:enable_redirect,1|exists:redirect_urls,group_code'
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
