<?php

declare(strict_types=1);

namespace App\Services\Prism;

use App\Models\Attachment;
use App\Models\Message;
use App\Services\Prism\Tools\AudioGenerationTool;
use App\Services\Prism\Tools\CalendarTool;
use App\Services\Prism\Tools\FileSystemTool;
use App\Services\Prism\Tools\ImageGenerationTool;
use App\Services\Prism\Tools\UserProfileTool;
use App\Services\Prism\Tools\VideoGenerationTool;
use Generator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Exceptions\PrismException;
use Prism\Prism\Exceptions\PrismRateLimitedException;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Streaming\Events\StreamEndEvent;
use Prism\Prism\Streaming\Events\TextDeltaEvent;
use Prism\Prism\Streaming\Events\ToolCallEvent;
use Prism\Prism\Streaming\Events\ToolResultEvent;
use Prism\Prism\Text\PendingRequest;
use Prism\Prism\ValueObjects\Media\Audio;
use Prism\Prism\ValueObjects\Media\Document;
use Prism\Prism\ValueObjects\Media\Image;
use Prism\Prism\ValueObjects\Media\Video;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;
use Prism\Prism\ValueObjects\Messages\UserMessage;

class PrismService
{
    public function __construct(
        private readonly SystemPromptBuilder $systemPromptBuilder,
        private readonly ProviderConfig $providerConfig,
    ) {}

    /**
     * @param  Collection<int, Message>  $messages
     * @return Generator<string>
     */
    public function streamResponse(string $selectedModel, Collection $messages): Generator
    {
        $parsed = $this->providerConfig->parseSelectedModel($selectedModel);

        if (! $parsed) {
            yield $this->formatError(__('chat.errors.no_model_selected'));

            return;
        }

        $provider = $this->providerConfig->getPrismProvider($parsed['provider']);

        if (! $provider) {
            yield $this->formatError(__('chat.errors.invalid_provider'));

            return;
        }

        $providerConfig = $this->providerConfig->getProviderConfig($parsed['provider']);

        if (empty($providerConfig)) {
            yield $this->formatError(__('chat.errors.provider_not_configured'));

            return;
        }

        try {
            $request = $this->buildRequest($provider, $parsed['model'], $providerConfig, $messages, $parsed['provider']);

            foreach ($request->asStream() as $event) {
                if ($event instanceof TextDeltaEvent) {
                    yield $event->delta;
                } elseif ($event instanceof ToolCallEvent) {
                    yield "\n\n<span class=\"tool-calling\">🪄 " . __('chat.tool_calling', ['tool' => str($event->toolCall->name)->headline()]) . "</span>\n\n";
                } elseif ($event instanceof ToolResultEvent) {
                    $result = $event->toolResult->result;
                    if (\is_string($result)) {
                        $decoded = json_decode($result, true);

                        if (isset($decoded['media']) && \is_array($decoded['media'])) {
                            yield "\n\n<!-- MEDIA_START -->" . json_encode($decoded['media']) . "<!-- MEDIA_END -->\n\n";
                        } elseif (isset($decoded['user_message'])) {
                            yield "\n\n{$decoded['user_message']}\n\n";
                        } elseif ($event->success && ! isset($decoded['media'])) {
                            yield "\n\n✅ " . __('chat.tool_success') . "\n\n";
                        } elseif (! $event->success) {
                            yield "\n\n❌ " . __('chat.tool_failed', ['error' => $event->error]) . "\n\n";
                        }
                    }
                } elseif ($event instanceof StreamEndEvent) {
                    // ... then, stream ended
                    // Don't break here - tool calls may follow
                }
            }
        } catch (PrismRateLimitedException $e) {
            $retryAfter = $e->retryAfter ? " {$e->retryAfter} seconds" : '';
            yield $this->formatError(__('chat.errors.rate_limited', ['retry' => $retryAfter]));
        } catch (PrismException $e) {
            yield $this->formatError($e->getMessage());
        } catch (\Throwable $e) {
            yield $this->formatError(__('chat.errors.unexpected', ['message' => $e->getMessage()]));
        }
    }

    /**
     * @param  array<string, mixed>  $providerConfig
     * @param  Collection<int, Message>  $messages
     */
    private function buildRequest(
        Provider $provider,
        string $model,
        array $providerConfig,
        Collection $messages,
        string $providerKey
    ): PendingRequest {
        return Prism::text()
            ->using($provider, $model, $providerConfig)
            ->withSystemPrompt($this->systemPromptBuilder->build())
            ->withMessages($this->convertMessages($messages, $providerKey))
            ->withTools($this->buildTools())
            ->withMaxSteps(3)
            ->withClientOptions([
                'timeout' => config('purrai.limits.timeout'), // timeout for AI requests
                'connect_timeout' => 30,
            ]);
    }

    /**
     * Build the list of available tools, including generation tools only if configured
     *
     * @return array<\Prism\Prism\Tool>
     */
    private function buildTools(): array
    {
        $tools = [
            CalendarTool::make(),
            UserProfileTool::make(),
            FileSystemTool::make(),
        ];

        $imageTool = ImageGenerationTool::make();
        if ($imageTool) {
            $tools[] = $imageTool;
        }

        $audioTool = AudioGenerationTool::make();
        if ($audioTool) {
            $tools[] = $audioTool;
        }

        $videoTool = VideoGenerationTool::make();
        if ($videoTool) {
            $tools[] = $videoTool;
        }

        return $tools;
    }

    /**
     * @param  Collection<int, Message>  $messages
     * @return array<int, UserMessage|AssistantMessage>
     */
    private function convertMessages(Collection $messages, string $providerKey): array
    {
        return $messages->map(function (Message $message) use ($providerKey) {
            if ($message->role === 'user') {
                $additionalContent = $this->buildAdditionalContent($message, $providerKey);

                return new UserMessage($message->content, $additionalContent);
            }

            return new AssistantMessage($message->content);
        })->toArray();
    }

    /**
     * @return array<int, Image|Audio|Video|Document>
     */
    private function buildAdditionalContent(Message $message, string $providerKey): array
    {
        $content = [];
        $supportedTypes = $this->providerConfig->getSupportedMediaTypes($providerKey);

        foreach ($message->attachments as $attachment) {
            $mediaType = $this->getMediaTypeFromMime($attachment->mime_type);

            if (! \in_array($mediaType, $supportedTypes, true)) {
                continue;
            }

            $media = $this->createMediaFromAttachment($attachment, $mediaType);

            if ($media) {
                $content[] = $media;
            }
        }

        return $content;
    }

    private function getMediaTypeFromMime(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        if (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        }

        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        return 'document';
    }

    private function createMediaFromAttachment(Attachment $attachment, string $mediaType): Image|Audio|Video|Document|null
    {
        $path = Storage::disk('local')->path($attachment->path);

        if (! file_exists($path)) {
            Log::warning('PrismService: Attachment file not found', [
                'path' => $path,
                'attachment_id' => $attachment->id,
                'attachment_path' => $attachment->path,
                'storage_path' => storage_path('app'),
            ]);

            return null;
        }

        return match ($mediaType) {
            'image' => Image::fromLocalPath($path),
            'audio' => Audio::fromLocalPath($path),
            'video' => Video::fromLocalPath($path),
            'document' => Document::fromLocalPath($path, $attachment->filename),
            default => null,
        };
    }

    private function formatError(?string $message = null): string
    {
        $message ??= 'An unknown error has occurred.';

        return "\n\n⚠️ **Error:** {$message}";
    }
}
