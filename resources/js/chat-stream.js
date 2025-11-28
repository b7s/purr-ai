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
 * Stream AI response from the server
 */
async function streamAIResponse(conversationId, selectedModel) {
    const streamingContainer = document.getElementById("streaming-response");
    const messagesContainer = document.getElementById("messages-container");

    if (!streamingContainer || !messagesContainer) {
        console.error("Required containers not found");
        return;
    }

    // Show streaming container
    streamingContainer.classList.remove("hidden");
    streamingContainer.innerHTML = "";

    let fullResponse = "";

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

        const reader = response.body.getReader();
        const decoder = new TextDecoder();

        while (true) {
            const { done, value } = await reader.read();

            if (done) break;

            const chunk = decoder.decode(value, { stream: true });
            const lines = chunk.split("\n");

            for (const line of lines) {
                if (line.startsWith("data: ")) {
                    try {
                        const data = JSON.parse(line.slice(6));

                        if (data.chunk) {
                            fullResponse += data.chunk;
                            renderMarkdown(streamingContainer, fullResponse);
                            scrollToBottom(messagesContainer);
                        }

                        if (data.done) {
                            // Stream complete - notify Livewire
                            notifyStreamComplete();
                        }
                    } catch (e) {
                        // Skip invalid JSON lines
                    }
                }
            }
        }
    } catch (error) {
        console.error("Stream error:", error);
        streamingContainer.innerHTML = `<div class="text-red-500">${
            window.chatTranslations?.stream_error ||
            "An error occurred while streaming the response."
        }</div>`;
        notifyStreamComplete();
    }
}

/**
 * Render markdown content safely
 */
function renderMarkdown(container, content) {
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

    container.innerHTML = sanitized;

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

    return sanitized;
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

// Export for global use
window.chatStream = {
    parseMarkdown,
    streamAIResponse,
};
