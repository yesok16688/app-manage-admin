<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppUrl;
use App\Models\UrlHandleLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UrlHandleLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $list = UrlHandleLog::query()
            ->with(['url', 'url.app'])
            ->when($request->get('url'), function(Builder $query, $value) {
                $query->whereHas('url', function(Builder $query1) use($value) {
                    $query1->where('url', 'like', "%$value%");
                });
            })
            ->when($request->get('http_status'), function(Builder $query, $value) {
                $query->where('http_status', $value);
            })
            ->when($request->get('client_ip'), function(Builder $query, $value) {
                $query->where('client_ip', 'like', "%$value%");
            })
            ->when(!is_null($request->get('status')), function(Builder $query) {
                $query->where('status', request()->get('status'));
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
        return $this->jsonResponse();
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $info = UrlHandleLog::query()->findOrFail($id);
        return $this->jsonResponse($info);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return $this->jsonResponse();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $info = UrlHandleLog::query()->findOrFail($id);
        $info->delete();
        return $this->jsonResponse();
    }

    public function handle(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'url_id' => 'required',
            'status' => 'required|in:1,2',
            'remark' => '',
        ]);
        $info = UrlHandleLog::query()->findOrFail($id);
        $info->update($data);

        $urlEnable = 0;
        if ($data['status'] == 1) {
            $urlEnable = 1;
        }
        $url = AppUrl::query()->findOrFail($data['url_id']);
        $url->update(['is_enable' => $urlEnable]);
        return $this->jsonResponse();
    }
}
