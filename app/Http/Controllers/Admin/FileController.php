<?php

namespace App\Http\Controllers\Admin;

use App\Enum\AppStatus;
use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\AppVersion;
use App\Utils\FileUtils\UploadUtil;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileController extends Controller
{
    /**
     * @throws \Exception
     */
    public function uploadIcon(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|image|mimes:jpg,png|max:200', // 限制每个文件最大为200KB
        ]);
        $saveFile = UploadUtil::upload2Local($request->file());
        return $this->jsonDataResponse($saveFile);
    }

    /**
     * @throws \Exception
     */
    public function uploadAppImages(Request $request): JsonResponse
    {
        $request->validate([
            'files.*' => 'required|file|max:10240', // 限制每个文件最大为10MB
        ]);
        $saveFiles = UploadUtil::batchUpload2Local($request->allFiles());
        return $this->jsonDataResponse($saveFiles);
    }

}
