/**
 * Chat Streaming Module
 * Handles AI response streaming with markdown rendering
 */

import { parseMarkdown } from "./chat-stream/markdown";
import {
    initMathObserver,
    renderMathInElement,
    scheduleMathRender,
} from "./chat-stream/math";
import { retryWithMessage, streamAIResponse } from "./chat-stream/stream";

function handleExternalLinkClicks(event) {
    const link = event.target.closest(
        '.chat-message a[href^="http"], .chat-message a[href^="https"]'
    );

    if (!link) {
        return;
    }

    event.preventDefault();
    const url = link.getAttribute("href");
    window.Livewire.dispatch("open-external-link", { url });
}

function initLivewireHooks() {
    document.addEventListener("livewire:init", () => {
        Livewire.on("start-ai-stream", (params) => {
            const data = Array.isArray(params) ? params[0] : params;
            if (data.conversationId && data.selectedModel) {
                streamAIResponse(data.conversationId, data.selectedModel);
            }
        });

        Livewire.hook("message.processed", () => {
            scheduleMathRender();
            initMathObserver();
        });
    });
}

function bootstrapMathObservers() {
    scheduleMathRender();
    initMathObserver();
}

document.addEventListener("click", handleExternalLinkClicks);
initLivewireHooks();

document.addEventListener("DOMContentLoaded", () => {
    bootstrapMathObservers();
});

window.renderMathInElement = renderMathInElement;
window.chatStream = {
    parseMarkdown,
    streamAIResponse,
    retryWithMessage,
};
