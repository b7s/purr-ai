/**
 * Chat Streaming Module
 * Handles AI response streaming with markdown rendering
 */

import { marked } from "marked";
import DOMPurify from "dompurify";

// Configure marked for safe rendering
marked.setOptions({
    breaks: true,
    gfm: true,
});

/**
 * Stream AI response from the server with retry logic
 */
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

    // Clear any previous content
    streamingContainer.innerHTML = "";

    // Show loading indicator
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
                // Check if we received any content
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
                if (line.startsWith("data: ")) {
                    try {
                        const data = JSON.parse(line.slice(6));

                        if (data.chunk) {
                            fullResponse += data.chunk;
                            // Only render if we have actual content (not just whitespace)
                            if (fullResponse.trim()) {
                                hasReceivedContent = true;
                                renderMarkdown(
                                    streamingContainer,
                                    fullResponse
                                );
                            }
                            scrollToBottom(messagesContainer);
                        }

                        if (data.done) {
                            // Hide loading indicator
                            hideLoadingIndicator();
                            // Stream complete - notify Livewire
                            notifyStreamComplete();
                        }
                    } catch (e) {
                        console.warn("Failed to parse SSE data:", e);
                        // Skip invalid JSON lines
                    }
                }
            }
        }

        // If we still haven't received content after retry, show error
        if (!hasReceivedContent) {
            throw new Error("No content received from stream");
        }
    } catch (error) {
        console.error("Stream error:", error);

        // Retry once if this is the first attempt
        if (retryCount === 0) {
            console.log("Retrying stream request...");
            setTimeout(() => {
                streamAIResponse(conversationId, selectedModel, retryCount + 1);
            }, 1000);
            return;
        }

        // Show error to user with retry button after retry failed
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
                class="px-4 py-2 bg-linear-to-br from-slate-900 to-slate-800 dark:from-slate-700 dark:to-slate-600 text-white rounded-lg hover:from-slate-800 hover:to-slate-700 dark:hover:from-slate-600 dark:hover:to-slate-500 transition-all duration-200 text-sm font-medium"
            >
                ${tryAgainText}
            </button>
        </div>`;
        hideLoadingIndicator();
        notifyStreamComplete();
    }
}

/**
 * Hide loading indicator
 */
function hideLoadingIndicator() {
    const loadingIndicator = document.getElementById(
        "stream-loading-indicator"
    );
    if (loadingIndicator) {
        loadingIndicator.style.display = "none";
    }
}

/**
 * Render markdown content safely
 */
function renderMarkdown(container, content) {
    try {
        // Check if content contains media markers
        const mediaMatch = content.match(
            /<!-- MEDIA_START -->([\s\S]*?)<!-- MEDIA_END -->/
        );
        let mediaHtml = "";

        if (mediaMatch) {
            try {
                const mediaJson = mediaMatch[1].trim();
                const mediaData = JSON.parse(mediaJson);
                if (Array.isArray(mediaData)) {
                    mediaHtml = renderMediaContent(mediaData);
                    // Remove the media markers and JSON from content
                    content = content.replace(mediaMatch[0], "").trim();
                }
            } catch (e) {
                console.warn("[warning] Failed to parse media JSON:", e);
            }
        }

        const html = marked.parse(content);
        const sanitized = DOMPurify.sanitize(html, {
            ALLOWED_TAGS: [
                "p",
                "br",
                "strong",
                "em",
                "code",
                "pre",
                "ul",
                "ol",
                "li",
                "a",
                "h1",
                "h2",
                "h3",
                "h4",
                "h5",
                "h6",
                "blockquote",
                "hr",
                "table",
                "thead",
                "tbody",
                "tr",
                "th",
                "td",
                "span",
                "div",
            ],
            ALLOWED_ATTR: ["href", "target", "rel", "class"],
            KEEP_CONTENT: true,
        });

        container.innerHTML = sanitized + mediaHtml;

        // Hide tool-calling messages that have responses after them
        hideCompletedToolCalls(container);

        // Add target="_blank" to external links
        container.querySelectorAll("a").forEach((link) => {
            if (link.href && !link.href.startsWith(window.location.origin)) {
                link.setAttribute("target", "_blank");
                link.setAttribute("rel", "noopener noreferrer");
            }
        });

        // Initialize copy buttons for code blocks
        if (window.initCodeCopyButtons) {
            window.initCodeCopyButtons();
        }
    } catch (error) {
        console.error("Markdown rendering error:", error);
        container.textContent = content; // Fallback to plain text
    }
}

/**
 * Hide tool-calling messages that have content after them
 */
function hideCompletedToolCalls(container) {
    const toolCallings = container.querySelectorAll(".tool-calling");
    const containerText = container.textContent || "";

    toolCallings.forEach((toolCall) => {
        const toolCallText = toolCall.textContent || "";

        // Get all text after this tool-calling in the container
        const toolCallIndex = containerText.indexOf(toolCallText);
        if (toolCallIndex !== -1) {
            const textAfter = containerText
                .substring(toolCallIndex + toolCallText.length)
                .trim();

            // If there's meaningful content after (not just whitespace or emojis)
            // and it's not just another tool-calling message
            if (
                textAfter.length > 0 &&
                !textAfter.startsWith("🔧") &&
                textAfter.replace(/[\s\n\r]/g, "").length > 0
            ) {
                toolCall.classList.add("completed");
            }
        }
    });
}

/**
 * Get CSRF token from meta tag
 */
function getCSRFToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute("content") : "";
}

/**
 * Scroll container to bottom
 */
function scrollToBottom(container) {
    if (container) {
        container.scrollTo({
            top: container.scrollHeight,
            behavior: "smooth",
        });
    }
}

/**
 * Notify Livewire that stream is complete
 */
function notifyStreamComplete() {
    const chatContainer = document.getElementById("messages-container");
    if (chatContainer) {
        const livewireEl = chatContainer.closest("[wire\\:id]");
        if (livewireEl) {
            const wireId = livewireEl.getAttribute("wire:id");
            const component = window.Livewire?.find(wireId);
            if (component) {
                component.call("streamComplete");
            }
        }
    }
}

/**
 * Parse markdown to HTML (exported for use in Blade)
 */
export function parseMarkdown(content) {
    // Check if content contains media markers
    const mediaMatch = content.match(
        /<!-- MEDIA_START -->([\s\S]*?)<!-- MEDIA_END -->/
    );
    let mediaHtml = "";

    if (mediaMatch) {
        try {
            const mediaJson = mediaMatch[1].trim();
            const mediaData = JSON.parse(mediaJson);
            if (Array.isArray(mediaData)) {
                mediaHtml = renderMediaContent(mediaData);
                // Remove the media markers and JSON from content
                content = content.replace(mediaMatch[0], "").trim();
            }
        } catch (e) {
            console.warn("[warning] Failed to parse media JSON:", e);
        }
    }

    const html = marked.parse(content);
    const sanitized = DOMPurify.sanitize(html, {
        ALLOWED_TAGS: [
            "p",
            "br",
            "strong",
            "em",
            "code",
            "pre",
            "ul",
            "ol",
            "li",
            "a",
            "h1",
            "h2",
            "h3",
            "h4",
            "h5",
            "h6",
            "blockquote",
            "hr",
            "table",
            "thead",
            "tbody",
            "tr",
            "th",
            "td",
            "span",
            "div",
        ],
        ALLOWED_ATTR: ["href", "target", "rel", "class"],
    });

    // Schedule copy button initialization after DOM update
    setTimeout(() => {
        if (window.initCodeCopyButtons) {
            window.initCodeCopyButtons();
        }
    }, 0);

    return sanitized + mediaHtml;
}

/**
 * Render media content (images, videos, audio)
 */
function renderMediaContent(mediaItems) {
    if (!Array.isArray(mediaItems) || mediaItems.length === 0) {
        return "";
    }

    let html = '<div class="media-display-container">';

    mediaItems.forEach((item) => {
        const type = item.type || "image";
        const url = item.url || "";
        const revisedPrompt = item.revised_prompt || "";

        if (!url) return;

        if (type === "image") {
            html += `
                <div class="media-item media-image">
                    <div class="media-wrapper">
                        <img 
                            src="${url}" 
                            alt="${revisedPrompt || "Generated image"}" 
                            class="media-content"
                            onclick="window.dispatchEvent(new CustomEvent('open-media-modal', { detail: { url: '${url}', type: 'image' } }))"
                        />
                    </div>
                    ${
                        revisedPrompt
                            ? `<p class="media-caption">${revisedPrompt}</p>`
                            : ""
                    }
                    <div class="media-actions">
                        <a href="${url}" download="generated-image.png" class="download-btn" title="Download">
                            <i class="iconoir-download"></i>
                        </a>
                    </div>
                </div>
            `;
        } else if (type === "video") {
            html += `
                <div class="media-item media-video">
                    <div class="media-wrapper">
                        <video src="${url}" controls class="media-content"></video>
                    </div>
                    <div class="media-actions">
                        <a href="${url}" download="generated-video.mp4" class="download-btn" title="Download">
                            <i class="iconoir-download"></i>
                        </a>
                    </div>
                </div>
            `;
        } else if (type === "audio") {
            html += `
                <div class="media-item media-audio">
                    <div class="media-wrapper">
                        <audio src="${url}" controls class="media-content"></audio>
                    </div>
                    <div class="media-actions">
                        <a href="${url}" download="generated-audio.mp3" class="download-btn" title="Download">
                            <i class="iconoir-download"></i>
                        </a>
                    </div>
                </div>
            `;
        }
    });

    html += "</div>";
    return html;
}

// Listen for Livewire events
document.addEventListener("livewire:init", () => {
    Livewire.on("start-ai-stream", (params) => {
        const data = Array.isArray(params) ? params[0] : params;
        if (data.conversationId && data.selectedModel) {
            streamAIResponse(data.conversationId, data.selectedModel);
        }
    });
});

/**
 * Retry by sending a message automatically
 */
function retryWithMessage(message) {
    const messageInput = document.getElementById("message-input");
    const sendButton = document.querySelector('[wire\\:click="sendMessage"]');

    if (messageInput && sendButton) {
        // Set the message
        messageInput.value = message;

        // Trigger input event to update Livewire
        messageInput.dispatchEvent(new Event("input", { bubbles: true }));

        // Small delay to ensure Livewire updates
        setTimeout(() => {
            sendButton.click();
        }, 100);
    }
}

// Export for global use
window.chatStream = {
    parseMarkdown,
    streamAIResponse,
    retryWithMessage,
};
