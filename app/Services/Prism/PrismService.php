<?php

declare(strict_types=1);

namespace App\Services\Prism;

use App\Models\Attachment;
use App\Models\Message;
use Generator;
use Illuminate\Support\Collection;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Exceptions\PrismException;
use Prism\Prism\Exceptions\PrismRateLimitedException;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Streaming\Events\StreamEndEvent;
use Prism\Prism\Streaming\Events\TextDeltaEvent;
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
                } elseif ($event instanceof StreamEndEvent) {
                    break;
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
        $request = Prism::text()
            ->using($provider, $model, $providerConfig)
            ->withSystemPrompt($this->systemPromptBuilder->build())
            ->withMessages($this->convertMessages($messages, $providerKey));

        return $request;
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
        $path = storage_path("app/{$attachment->path}");

        if (! file_exists($path)) {
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
