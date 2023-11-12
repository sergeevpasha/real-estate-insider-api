<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class FileStorageController extends Controller
{
    /**
     * @param string $name
     *
     * @return Response
     */
    public function viewable(string $name): Response
    {
        $disk = config('filesystems')['default'];

        if (Storage::disk($disk)->exists("/private/$name")) {
            return Storage::disk($disk)->response("/private/$name");
        }

        return app()-response()->abort(404);
    }

    /**
     * @param Request $request
     * @param string                   $path
     *
     * @return Response
     */
    public function downloadable(Request $request, string $path): Response
    {
        $name = urldecode($request->query('name', basename($path))) ?: null;
        if (Storage::exists("/private/$path")) {
            return Storage::download("/private/$path", $name);
        }

        return app()-response()->abort(404);
    }
}
