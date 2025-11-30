import { marked } from "marked";
import DOMPurify from "dompurify";
import {
    initMathObserver,
    protectMathExpressions,
    renderMathInElement,
    restoreMathExpressions,
    scheduleMathRender,
} from "./math";

marked.setOptions({
    breaks: true,
    gfm: true,
});

function extractMediaBlock(content) {
    const mediaMatch = content.match(
        /<!-- MEDIA_START -->([\s\S]*?)<!-- MEDIA_END -->/
    );

    if (!mediaMatch) {
        return { content, mediaHtml: "" };
    }

    try {
        const mediaJson = mediaMatch[1].trim();
        const mediaData = JSON.parse(mediaJson);
        const mediaHtml = Array.isArray(mediaData)
            ? renderMediaContent(mediaData)
            : "";

        return {
            content: content.replace(mediaMatch[0], "").trim(),
            mediaHtml,
        };
    } catch (error) {
        console.warn("[warning] Failed to parse media JSON:", error);
        return { content, mediaHtml: "" };
    }
}

function renderMediaContent(mediaItems) {
    if (!Array.isArray(mediaItems) || mediaItems.length === 0) {
        return "";
    }

    let html = '<div class="media-display-container">';

    mediaItems.forEach((item) => {
        const type = item.type || "image";
        const url = item.url || "";
        const revisedPrompt = item.revised_prompt || "";

        if (!url) {
            return;
        }

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

function hideCompletedToolCalls(container) {
    const toolCallings = container.querySelectorAll(".tool-calling");
    const containerText = container.textContent || "";

    toolCallings.forEach((toolCall) => {
        const toolCallText = toolCall.textContent || "";
        const toolCallIndex = containerText.indexOf(toolCallText);

        if (toolCallIndex === -1) {
            return;
        }

        const textAfter = containerText
            .substring(toolCallIndex + toolCallText.length)
            .trim();

        if (
            textAfter.length > 0 &&
            !textAfter.startsWith("🔧") &&
            textAfter.replace(/[\s\n\r]/g, "").length > 0
        ) {
            toolCall.classList.add("completed");
        }
    });
}

function renderMarkdown(container, content) {
    const originalContent = content;

    try {
        const { content: textContent, mediaHtml } = extractMediaBlock(content);
        const { protectedContent, mathExpressions } =
            protectMathExpressions(textContent);

        let html = marked.parse(protectedContent);

        if (mathExpressions.length > 0) {
            html = restoreMathExpressions(html, mathExpressions);
        }

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

        renderMathInElement(container);
        initMathObserver();
        hideCompletedToolCalls(container);

        container.querySelectorAll("a").forEach((link) => {
            if (link.href && !link.href.startsWith(window.location.origin)) {
                link.setAttribute("target", "_blank");
                link.setAttribute("rel", "noopener noreferrer");
            }
        });

        if (window.initCodeCopyButtons) {
            window.initCodeCopyButtons();
        }
    } catch (error) {
        console.error("Markdown rendering error:", error);
        container.textContent = originalContent;
    }
}

function parseMarkdown(content) {
    const originalContent = content;

    try {
        const { content: textContent, mediaHtml } = extractMediaBlock(content);
        const { protectedContent, mathExpressions } =
            protectMathExpressions(textContent);

        let html = marked.parse(protectedContent);

        if (mathExpressions.length > 0) {
            html = restoreMathExpressions(html, mathExpressions);
        }

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

        setTimeout(() => {
            if (window.initCodeCopyButtons) {
                window.initCodeCopyButtons();
            }
            scheduleMathRender();
            initMathObserver();
        }, 0);

        return sanitized + mediaHtml;
    } catch (error) {
        console.error("Markdown parsing error:", error);
        return originalContent;
    }
}

export { parseMarkdown, renderMarkdown };
