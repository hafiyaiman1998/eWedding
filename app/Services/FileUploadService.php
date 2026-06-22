<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    /**
     * Extensions treated as video uploads when bucketing user-card files.
     *
     * @var array<int, string>
     */
    private const VIDEO_EXTENSIONS = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'];

    /**
     * Store a user-card variable file, bucketing it by detected type and user.
     *
     * Mirrors the historical UserCardController store()/update() behavior:
     * video extensions go under a "videos" sub-directory, everything else
     * under "images", scoped to the owning user id.
     */
    public function storeUserCardFile(UploadedFile $file, int|string $userId): string
    {
        $isVideo = in_array($file->getClientOriginalExtension(), self::VIDEO_EXTENSIONS);
        $fileType = $isVideo ? 'videos' : 'images';
        $storagePath = 'user-card/'.$userId."/{$fileType}";

        return $file->store($storagePath, 'public');
    }

    /**
     * Store a template-variable file using the "template-var-" naming convention.
     *
     * Mirrors the historical AdminTemplateController upload* behavior.
     */
    public function storeTemplateVariableFile(UploadedFile $file, string $variableName): string
    {
        $filename = 'template-var-'.$variableName.'-'.time().'.'.$file->getClientOriginalExtension();

        return $file->storeAs('template-variables', $filename, 'public');
    }

    /**
     * Delete a previously stored file by its public-disk relative path.
     */
    public function deleteByPath(?string $path): void
    {
        if (! empty($path) && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Delete a file referenced by a public storage URL.
     *
     * Only files served from "/storage/" are eligible, preventing deletion of
     * external/non-application files. Returns whether a file was deleted.
     */
    public function deleteByUrl(?string $url): bool
    {
        if ($url !== null && strpos($url, '/storage/') !== false) {
            $path = str_replace('/storage/', '', parse_url($url, PHP_URL_PATH));

            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->delete($path);
            }
        }

        return false;
    }
}
