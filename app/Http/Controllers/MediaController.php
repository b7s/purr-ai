<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController extends Controller
{
    public function serve(string $path): Response|StreamedResponse
    {
        if (! Storage::exists($path)) {
            abort(404);
        }

        $allowedPaths = ['generated_images/', 'generated_audio/', 'generated_video/', 'attachments/'];
        $isAllowed = false;

        foreach ($allowedPaths as $allowedPath) {
            if (str_starts_with($path, $allowedPath)) {
                $isAllowed = true;
                break;
            }
        }

        if (! $isAllowed) {
            abort(403);
        }

        $mimeType = Storage::mimeType($path);

        return response()->stream(
            function () use ($path) {
                $stream = Storage::readStream($path);
                if ($stream) {
                    fpassthru($stream);
                    fclose($stream);
                }
            },
            200,
            [
                'Content-Type' => $mimeType,
                'Content-Length' => Storage::size($path),
                'Cache-Control' => 'public, max-age=31536000',
            ]
        );
    }
}
