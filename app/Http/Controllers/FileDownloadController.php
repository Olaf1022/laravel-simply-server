<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileDownloadController extends Controller
{
    public function download(Request $request)
    {
        $file = File::where('uuid', $request->uuid)->firstOrFail();

        // Prevent downloading private files, except by the logged-in owner
        if (!$file->public && ($file->user_id !== auth()->id() || auth()->guest())) {
            abort(404);
        }

        $headers = [
            'Content-Type' => $file->mime ?? 'application/octet-stream',
            'Content-Length' => Storage::size($file->path),
            'Content-Disposition' => "attachment; filename=\"{$file->name}\"",
        ];

        if (Storage::exists($file->path)) {
            $file->trackDownload($request);

            return response()->streamDownload(function () use ($file) {
                echo Storage::get($file->path);
            }, $file->name, $headers);
        } else {
            abort(404);
        }
    }
}
