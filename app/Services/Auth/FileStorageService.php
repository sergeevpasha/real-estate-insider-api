<?php

declare(strict_types=1);

namespace App\Services\Auth;

use finfo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

readonly class FileStorageService
{
    private const EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
    ];

    /**
     * @param string $url
     * @return UploadedFile
     */
    public function downloadExternalImage(string $url): UploadedFile
    {
        $imageContent = Http::get($url)->body();
        $fInfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $fInfo->buffer($imageContent);
        $extension = self::EXTENSIONS[$mimeType];

        $name = Str::random(40) . '.' . $extension;
        $tempFilePath = sys_get_temp_dir() . '/' . $name;
        File::put($tempFilePath, $imageContent);

        return new UploadedFile(
            $tempFilePath,
            $name,
            $mimeType,
        );
    }

    /**
     * @param UploadedFile $file
     * @param string $path
     * @param string|null $disk
     * @return string
     */
    public function storeFile(UploadedFile $file, string $path, ?string $disk = null): string
    {
        if (!$disk) {
            $disk = config('filesystems')['default'];
        }

        $trimmedPath = trim($path, '/');
        $name = $this->generateHashName($file);
        Storage::disk($disk)->putFileAs("/private/$trimmedPath", $file, $name);

        if (file_exists($file->path())) {
            unlink($file->path());
        }

        return "/$trimmedPath/$name";
    }

    /**
     * @param string $path
     * @param string|null $disk
     * @return void
     */
    public function deleteFile(string $path, ?string $disk = null): void
    {
        $trimmedPath = trim($path, '/');

        if (!$disk) {
            $disk = config('filesystems')['default'];
        }

        if (Storage::exists("/private/$trimmedPath")) {
            Storage::disk($disk)->delete("/private/$trimmedPath");
        }
    }

    /**
     * Generate Hash Name
     *
     * @param UploadedFile $file
     *
     * @return string
     */
    private function generateHashName(UploadedFile $file): string
    {
        return md5($file->hashName()) . time() . '.' . $file->getClientOriginalExtension();
    }
}