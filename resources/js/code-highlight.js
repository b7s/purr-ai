/**
 * Code Syntax Highlighting
 * Applies syntax highlighting to code blocks after streaming completes
 */

import hljs from "highlight.js/lib/core";

// Import only commonly used languages to keep bundle size small
import javascript from "highlight.js/lib/languages/javascript";
import typescript from "highlight.js/lib/languages/typescript";
import python from "highlight.js/lib/languages/python";
import php from "highlight.js/lib/languages/php";
import java from "highlight.js/lib/languages/java";
import cpp from "highlight.js/lib/languages/cpp";
import csharp from "highlight.js/lib/languages/csharp";
import go from "highlight.js/lib/languages/go";
import rust from "highlight.js/lib/languages/rust";
import ruby from "highlight.js/lib/languages/ruby";
import sql from "highlight.js/lib/languages/sql";
import json from "highlight.js/lib/languages/json";
import xml from "highlight.js/lib/languages/xml";
import css from "highlight.js/lib/languages/css";
import bash from "highlight.js/lib/languages/bash";
import shell from "highlight.js/lib/languages/shell";
import yaml from "highlight.js/lib/languages/yaml";
import markdown from "highlight.js/lib/languages/markdown";
import swift from "highlight.js/lib/languages/swift";
import kotlin from "highlight.js/lib/languages/kotlin";

// Register languages
hljs.registerLanguage("javascript", javascript);
hljs.registerLanguage("js", javascript);
hljs.registerLanguage("typescript", typescript);
hljs.registerLanguage("ts", typescript);
hljs.registerLanguage("python", python);
hljs.registerLanguage("py", python);
hljs.registerLanguage("php", php);
hljs.registerLanguage("java", java);
hljs.registerLanguage("cpp", cpp);
hljs.registerLanguage("c++", cpp);
hljs.registerLanguage("csharp", csharp);
hljs.registerLanguage("cs", csharp);
hljs.registerLanguage("go", go);
hljs.registerLanguage("rust", rust);
hljs.registerLanguage("ruby", ruby);
hljs.registerLanguage("rb", ruby);
hljs.registerLanguage("sql", sql);
hljs.registerLanguage("json", json);
hljs.registerLanguage("xml", xml);
hljs.registerLanguage("html", xml);
hljs.registerLanguage("css", css);
hljs.registerLanguage("bash", bash);
hljs.registerLanguage("shell", shell);
hljs.registerLanguage("sh", shell);
hljs.registerLanguage("yaml", yaml);
hljs.registerLanguage("yml", yaml);
hljs.registerLanguage("markdown", markdown);
hljs.registerLanguage("md", markdown);
hljs.registerLanguage("swift", swift);
hljs.registerLanguage("kotlin", kotlin);
hljs.registerLanguage("kt", kotlin);

/**
 * Detect language from code block class or content
 */
function detectLanguage(codeElement) {
    // Check for language class (e.g., language-javascript, lang-js)
    const classes = codeElement.className.split(" ");
    for (const cls of classes) {
        if (cls.startsWith("language-")) {
            return cls.replace("language-", "");
        }
        if (cls.startsWith("lang-")) {
            return cls.replace("lang-", "");
        }
    }

    // Try auto-detection
    return null;
}

/**
 * Apply syntax highlighting to a code block
 */
function highlightCodeBlock(preElement) {
    const codeElement = preElement.querySelector("code");
    if (!codeElement) return;

    // Skip if already highlighted
    if (codeElement.hasAttribute("data-highlighted")) {
        return;
    }

    try {
        const language = detectLanguage(codeElement);
        const code = codeElement.textContent || "";

        let result;
        if (language && hljs.getLanguage(language)) {
            // Highlight with specific language
            result = hljs.highlight(code, { language });
        } else {
            // Auto-detect language
            result = hljs.highlightAuto(code);
        }

        // Apply highlighting
        codeElement.innerHTML = result.value;
        codeElement.setAttribute("data-highlighted", "true");

        // Add detected language class if auto-detected
        if (!language && result.language) {
            codeElement.classList.add(`language-${result.language}`);

            // Add language label
            addLanguageLabel(preElement, result.language);
        } else if (language) {
            addLanguageLabel(preElement, language);
        }
    } catch (error) {
        console.warn("Failed to highlight code block:", error);
        // Mark as highlighted to prevent retry
        codeElement.setAttribute("data-highlighted", "true");
    }
}

/**
 * Add language label to code block
 */
function addLanguageLabel(preElement, language) {
    // Check if label already exists
    if (preElement.querySelector(".code-language-label")) {
        return;
    }

    const label = document.createElement("span");
    label.className = "code-language-label";
    label.textContent = language;
    preElement.appendChild(label);
}

/**
 * Highlight all code blocks in container
 */
export function highlightCodeBlocks(container = document) {
    const preElements = container.querySelectorAll(
        "pre:not([data-highlight-processed])"
    );

    preElements.forEach((preElement) => {
        highlightCodeBlock(preElement);
        preElement.setAttribute("data-highlight-processed", "true");
    });
}

/**
 * Highlight code blocks after streaming completes
 * This is called after the AI finishes streaming a response
 */
export function highlightStreamedCode() {
    const streamingContainer = document.getElementById("streaming-response");
    if (streamingContainer) {
        highlightCodeBlocks(streamingContainer);
    }
}

/**
 * Initialize highlighting for existing code blocks
 */
document.addEventListener("DOMContentLoaded", () => {
    highlightCodeBlocks();
});

/**
 * Watch for new code blocks (for dynamic content)
 */
if (typeof MutationObserver !== "undefined") {
    const observer = new MutationObserver((mutations) => {
        let shouldHighlight = false;

        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1) {
                    // Element node
                    if (node.tagName === "PRE" || node.querySelector("pre")) {
                        shouldHighlight = true;
                    }
                }
            });
        });

        if (shouldHighlight) {
            // Small delay to ensure content is fully rendered
            setTimeout(() => {
                highlightCodeBlocks();
            }, 50);
        }
    });

    document.addEventListener("DOMContentLoaded", () => {
        observer.observe(document.body, {
            childList: true,
            subtree: true,
        });
    });
}

// Export for global use
window.highlightCodeBlocks = highlightCodeBlocks;
window.highlightStreamedCode = highlightStreamedCode;
