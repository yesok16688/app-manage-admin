<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Utils\FileUtils\UploadUtil;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    public function image(string $id): StreamedResponse
    {
        $fileInfo = File::query()->find($id);
        if (!$fileInfo) {
            abort(404);
        }
        return Storage::response(
            $fileInfo->save_path,
            $fileInfo->file_name,
            ['Content-Type', Storage::mimeType($fileInfo->file_name)]
        );
    }


    /**
     * @throws \Exception
     */
    public function uploadIcon(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|image|mimes:jpg,png|max:500|dimensions:ratio=1', // 限制每个文件最大为500KB
        ], [
            'file.max' => '图标大小不能大于500K',
            'file.dimensions' => '图标比例必须为1:1'
        ]);
        $saveFile = UploadUtil::upload2Local($request->file('file'));
        return $this->jsonDataResponse($saveFile);
    }

    /**
     * @throws \Exception
     */
    public function uploadAppImages(Request $request): JsonResponse
    {
        $request->validate([
            'files.*' => 'required|file|max:5120', // 限制每个文件最大为5MB
        ]);
        $saveFiles = UploadUtil::batchUpload2Local($request->file('files'));
        return $this->jsonDataResponse($saveFiles);
    }

}
