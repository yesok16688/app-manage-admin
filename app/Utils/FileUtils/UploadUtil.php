<?php

namespace App\Utils\FileUtils;

use App\Models\File;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadUtil
{
    /**
     * @throws Exception
     */
    public static function upload2Local(UploadedFile $uploadedFile, $mode = 'private'): File
    {
        $datePath = Carbon::now()->format('Y/m/d');
        $uuid = Str::uuid()->toString();
        $uuidWithoutDashes = str_replace('-', '', $uuid);
        $extension = $uploadedFile->getClientOriginalExtension();
        $saveName = "{$uuidWithoutDashes}.{$extension}";
        $filePath = "{$mode}/{$datePath}/$saveName";
        Storage::put($filePath, file_get_contents($uploadedFile));
        $fileSaveInfo = new File([
            'id' => $uuid,
            'file_name' => $saveName,
            'save_path' => $filePath,
            'origin_name' => $uploadedFile->getClientOriginalName(),
            'extension' => $extension,
            'file_size' => $uploadedFile->getSize()
        ]);
        try {
            $fileSaveInfo->save();
        } catch (Exception $exception) {
            Storage::delete($filePath);
            throw $exception;
        }
        return $fileSaveInfo;
    }

    /**
     * @throws Exception
     */
    public static function batchUpload2Local(array $uploadedFiles, $mode = 'private'): array
    {
        $fileSaveList = [];
        try {
            foreach($uploadedFiles as $uploadedFile) {
                $fileSaveList[] = self::upload2Local($uploadedFile, $mode);
            }
        } catch (Exception $exception) {
            foreach ($fileSaveList as $item) {
                Storage::delete($item['save_path']);
            }
            throw $exception;
        }
        return $fileSaveList;
    }
}
