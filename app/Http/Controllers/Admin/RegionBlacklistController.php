<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegionBlacklist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RegionBlacklistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $list = RegionBlacklist::query()
            ->where('type', 0)
            ->when($request->get('region_code'), function(Builder $query, $value) {
                $query->where('region_code', $value);
            })
            ->when(!is_null($request->get('is_enable')), function(Builder $query) {
                $query->where('is_enable', request()->get('is_enable'));
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
            'region_code' => 'required|max:2|exists:regions,iso_code',
            'sub_region_codes' => 'array',
            'sub_region_codes.*' => 'max:3|exists:sub_regions,iso_code',
            'is_enable' => 'required|in:0,1',
        ]);
        $data['type'] = 0;
        RegionBlacklist::create($data);
        return $this->jsonResponse();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $info = RegionBlacklist::query()
            ->where('type', 0)
            ->findOrFail($id);
        return $this->jsonResponse($info);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'region_code' => 'max:2|exists:regions,iso_code',
            'sub_region_codes' => 'array',
            'sub_region_codes.*' => 'max:3|exists:sub_regions,iso_code',
            'is_enable' => 'in:0,1',
        ]);
        $info = RegionBlacklist::query()
            ->where('type', 0)
            ->findOrFail($id);
        $info->update($data);
        return $this->jsonResponse();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $info = RegionBlacklist::query()
            ->where('type', 0)
            ->findOrFail($id);
        $info->delete();
        return $this->jsonResponse();
    }
}
