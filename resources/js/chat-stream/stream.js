import { renderMarkdown } from "./markdown";
import { renderMathInElement } from "./math";

async function streamAIResponse(conversationId, selectedModel, retryCount = 0) {
    const streamingContainer = document.getElementById("streaming-response");
    const messagesContainer = document.getElementById("messages-container");
    const loadingIndicator = document.getElementById(
        "stream-loading-indicator"
    );

    if (!streamingContainer || !messagesContainer) {
        console.error("Required containers not found");
        return;
    }

    streamingContainer.innerHTML = "";

    if (loadingIndicator) {
        loadingIndicator.classList.remove("hidden");
    }

    let fullResponse = "";
    let hasReceivedContent = false;

    try {
        const response = await fetch("/api/chat/stream", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": getCSRFToken(),
                Accept: "text/event-stream",
            },
            body: JSON.stringify({
                conversation_id: conversationId,
                selected_model: selectedModel,
            }),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        if (!response.body) {
            throw new Error("Response body is null");
        }

        const reader = response.body.getReader();
        const decoder = new TextDecoder();

        while (true) {
            const { done, value } = await reader.read();

            if (done) {
                if (!hasReceivedContent && retryCount === 0) {
                    console.warn(
                        "Stream ended without content, retrying once..."
                    );
                    return streamAIResponse(
                        conversationId,
                        selectedModel,
                        retryCount + 1
                    );
                }
                break;
            }

            const chunk = decoder.decode(value, { stream: true });
            const lines = chunk.split("\n");

            for (const line of lines) {
                if (!line.startsWith("data: ")) {
                    continue;
                }

                try {
                    const data = JSON.parse(line.slice(6));

                    if (data.chunk) {
                        fullResponse += data.chunk;

                        if (fullResponse.trim()) {
                            hasReceivedContent = true;
                            renderMarkdown(streamingContainer, fullResponse);
                        }
                        scrollToBottom(messagesContainer);
                    }

                    if (data.done) {
                        hideLoadingIndicator();

                        if (window.highlightStreamedCode) {
                            window.highlightStreamedCode();
                        }

                        renderMathInElement(streamingContainer);
                        notifyStreamComplete();
                    }
                } catch (error) {
                    console.warn("Failed to parse SSE data:", error);
                }
            }
        }

        if (!hasReceivedContent) {
            throw new Error("No content received from stream");
        }
    } catch (error) {
        console.error("Stream error:", error);

        if (retryCount === 0) {
            console.log("Retrying stream request...");
            setTimeout(() => {
                streamAIResponse(conversationId, selectedModel, retryCount + 1);
            }, 1000);
            return;
        }

        const errorMessage =
            window.chatTranslations?.stream_error || "Failed to load response";
        const tryAgainText = window.chatTranslations?.try_again || "Try Again";
        const retryMessage =
            window.chatTranslations?.retry_message ||
            "What happened? Please try again.";

        streamingContainer.innerHTML = `<div class="text-red-500 text-sm">
            <p class="font-medium mb-2">😔 ${errorMessage}</p>
            <p class="text-xs opacity-80 mb-3">${error.message}</p>
            <button 
                onclick="window.chatStream.retryWithMessage('${retryMessage.replace(
                    /'/g,
                    "\\'"
                )}')"
                class="px-4 py-2 bg-linear-to-br from-slate-900 to-slate-800 dark:from-slate-700 dark:to-slate-600 text-white rounded-xl hover:from-slate-800 hover:to-slate-700 dark:hover:from-slate-600 dark:hover:to-slate-500 transition-all duration-200 text-sm font-medium"
            >
                ${tryAgainText}
            </button>
        </div>`;

        hideLoadingIndicator();
        notifyStreamComplete();
    }
}

function hideLoadingIndicator() {
    const loadingIndicator = document.getElementById(
        "stream-loading-indicator"
    );
    if (loadingIndicator) {
        loadingIndicator.style.display = "none";
    }
}

function getCSRFToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute("content") : "";
}

function scrollToBottom(container) {
    if (container) {
        container.scrollTo({
            top: container.scrollHeight,
            behavior: "smooth",
        });
    }
}

function notifyStreamComplete() {
    const chatContainer = document.getElementById("messages-container");
    if (chatContainer) {
        const livewireEl = chatContainer.closest("[wire\\:id]");
        if (livewireEl) {
            const wireId = livewireEl.getAttribute("wire:id");
            const component = window.Livewire?.find(wireId);
            if (component) {
                component.call("streamComplete", document.hasFocus());
            }
        }
    }
}

function retryWithMessage(message) {
    const messageInput = document.getElementById("message-input");
    const sendButton = document.querySelector('[wire\\:click="sendMessage"]');

    if (messageInput && sendButton) {
        messageInput.value = message;
        messageInput.dispatchEvent(new Event("input", { bubbles: true }));

        setTimeout(() => {
            sendButton.click();
        }, 100);
    }
}

export { retryWithMessage, streamAIResponse };
