<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\WhisperService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Facades\Prism;
use Prism\Prism\ValueObjects\Media\Audio;

final class TranscribeController extends Controller
{
    public function __construct(
        private readonly WhisperService $whisperService
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'audio' => ['required', 'file', 'max:25600'],
        ]);

        $audioFile = $request->file('audio');

        if (! $audioFile) {
            return response()->json([
                'success' => false,
                'text' => '',
                'error' => 'No audio file provided',
            ], 400);
        }

        // Check if using local or online speech
        $useLocal = (int) Setting::get('use_local_speech') === 1;

        if ($useLocal) {
            return $this->transcribeLocal($audioFile);
        }

        return $this->transcribeOnline($audioFile);
    }

    private function transcribeLocal(UploadedFile $audioFile): JsonResponse
    {
        if (! $this->whisperService->isAvailable()) {
            return response()->json([
                'success' => false,
                'text' => '',
                'error' => 'Speech recognition is not configured. Please check Settings.',
            ], 503);
        }

        try {
            $text = $this->whisperService->transcribe($audioFile);

            return response()->json([
                'success' => true,
                'text' => $text,
            ]);
        } catch (\Exception $e) {
            Log::error('Local transcription failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'text' => '',
                'error' => 'Transcription failed: '.$e->getMessage(),
            ], 500);
        }
    }

    private function transcribeOnline(UploadedFile $audioFile): JsonResponse
    {
        $speechProviderValue = Setting::get('speech_provider');
        if (empty($speechProviderValue)) {
            return response()->json([
                'success' => false,
                'text' => '',
                'error' => 'Speech provider not configured. Please check Settings.',
            ], 503);
        }

        $parts = explode(':', $speechProviderValue);
        if (\count($parts) < 2) {
            return response()->json([
                'success' => false,
                'text' => '',
                'error' => 'Invalid speech provider configuration.',
            ], 503);
        }

        [$provider, $model] = $parts;

        // Get API key for the provider
        $apiKey = Setting::getProviderApiKey($provider);
        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'text' => '',
                'error' => 'API key not configured for provider. Please check Settings.',
            ], 503);
        }

        try {
            // Convert audio to a compatible format (mp3) for OpenAI
            $convertedPath = $this->convertAudioToMp3($audioFile);

            if (! $convertedPath) {
                return response()->json([
                    'success' => false,
                    'text' => '',
                    'error' => 'Failed to convert audio file.',
                ], 500);
            }

            try {
                // Read file content and create Audio object
                $audioContent = file_get_contents($convertedPath);
                $audio = Audio::fromRawContent($audioContent, 'audio/mpeg')
                    ->as('recording.mp3');

                $response = Prism::audio()
                    ->using($provider, 'whisper-1', ['api_key' => $apiKey])
                    ->withInput($audio)
                    ->asText();

                return response()->json([
                    'success' => true,
                    'text' => $response->text,
                ]);
            } finally {
                // Clean up converted file
                if (file_exists($convertedPath)) {
                    @unlink($convertedPath);
                }
            }
        } catch (\Exception $e) {
            Log::error('Online transcription failed', [
                'provider' => $provider,
                'model' => $model,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'text' => '',
                'error' => 'Transcription failed: '.$e->getMessage(),
            ], 500);
        }
    }

    private function convertAudioToMp3(UploadedFile $audioFile): ?string
    {
        try {
            // Save uploaded file temporarily
            $tempInput = sys_get_temp_dir().'/audio_input_'.uniqid().'.webm';
            $audioFile->move(dirname($tempInput), basename($tempInput));

            // Output as mp3
            $tempOutput = sys_get_temp_dir().'/audio_output_'.uniqid().'.mp3';

            // Use ffmpeg to convert to mp3 (lightweight and compatible)
            $ffmpegPath = $this->whisperService->getFfmpegPath();

            $result = \Illuminate\Support\Facades\Process::run([
                $ffmpegPath,
                '-i', $tempInput,
                '-vn',
                '-ar', '16000',
                '-ac', '1',
                '-b:a', '32k',
                '-f', 'mp3',
                '-y',
                $tempOutput,
            ]);

            // Clean up input file
            @unlink($tempInput);

            if (! $result->successful()) {
                Log::error('FFmpeg conversion failed', [
                    'error' => $result->errorOutput(),
                ]);

                return null;
            }

            return $tempOutput;
        } catch (\Exception $e) {
            Log::error('Audio conversion failed', ['error' => $e->getMessage()]);

            return null;
        }
    }
}
