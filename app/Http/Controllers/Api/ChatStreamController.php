<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\Prism\PrismService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatStreamController extends Controller
{
    public function __construct(
        private readonly PrismService $prismService,
    ) {}

    public function __invoke(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'conversation_id' => 'required|integer|exists:conversations,id',
            'selected_model' => 'required|string',
        ]);

        $conversationId = (int) $validated['conversation_id'];
        $selectedModel = $validated['selected_model'];

        $conversation = Conversation::with('messages.attachments')
            ->findOrFail($conversationId);

        return response()->stream(function () use ($conversation, $selectedModel, $conversationId): void {
            $fullResponse = '';

            foreach ($this->prismService->streamResponse($selectedModel, $conversation->messages) as $chunk) {
                $fullResponse .= $chunk;

                echo 'data: '.json_encode(['chunk' => $chunk])."\n\n";

                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
            }

            $this->saveAssistantMessage($conversationId, $fullResponse);

            echo 'data: '.json_encode(['done' => true, 'message_id' => Str::uuid()->toString()])."\n\n";

            if (ob_get_level() > 0) {
                ob_flush();
            }
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    private function saveAssistantMessage(int $conversationId, string $content): void
    {
        if (empty(trim($content))) {
            return;
        }

        Message::create([
            'conversation_id' => $conversationId,
            'role' => 'assistant',
            'content' => trim($content),
        ]);

        Conversation::where('id', $conversationId)->touch();
    }
}
