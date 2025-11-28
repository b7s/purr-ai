/**
 * Code Copy Functionality
 * Adds copy buttons to code blocks
 */

/**
 * Get translation with fallback
 */
function t(key, fallback) {
    return window.chatTranslations?.[key] || fallback;
}

/**
 * Get copy icon SVG
 */
function getCopyIcon() {
    return `
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
        </svg>
    `;
}

/**
 * Get check icon SVG
 */
function getCheckIcon() {
    return `
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M5 13l4 4L19 7" />
        </svg>
    `;
}

/**
 * Add copy buttons to all pre elements
 */
export function initCodeCopyButtons() {
    // Find all pre elements that don't already have a copy button
    const preElements = document.querySelectorAll(
        "pre:not([data-copy-initialized])"
    );

    preElements.forEach((pre) => {
        addCopyButton(pre);
        pre.setAttribute("data-copy-initialized", "true");
    });
}

/**
 * Add a copy button to a specific pre element
 */
function addCopyButton(preElement) {
    // Create copy button
    const button = document.createElement("button");
    button.className = "code-copy-btn";
    button.innerHTML = getCopyIcon();
    button.setAttribute("type", "button");
    button.setAttribute("title", t("code_copy", "Copy code"));

    // Add click handler
    button.addEventListener("click", async () => {
        await copyCode(preElement, button);
    });

    // Add button to pre element
    preElement.style.position = "relative";
    preElement.appendChild(button);
}

/**
 * Copy code from pre element to clipboard
 */
async function copyCode(preElement, button) {
    // Get code content (excluding the button)
    const codeElement = preElement.querySelector("code");
    const text = codeElement ? codeElement.textContent : preElement.textContent;

    // Clean the text
    const cleanText = text.trim();

    try {
        await navigator.clipboard.writeText(cleanText);

        // Show success feedback
        button.classList.add("copied");
        button.innerHTML = getCheckIcon();
        button.setAttribute("title", t("code_copied", "Copied!"));

        // Reset after 2 seconds
        setTimeout(() => {
            button.classList.remove("copied");
            button.innerHTML = getCopyIcon();
            button.setAttribute("title", t("code_copy", "Copy code"));
        }, 500);
    } catch (err) {
        console.error("Failed to copy code:", err);
    }
}

/**
 * Initialize on DOM ready
 */
document.addEventListener("DOMContentLoaded", () => {
    initCodeCopyButtons();
});

/**
 * Re-initialize when new content is added (for dynamic content)
 */
if (typeof MutationObserver !== "undefined") {
    const observer = new MutationObserver((mutations) => {
        let shouldInit = false;

        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1) {
                    // Element node
                    if (node.tagName === "PRE" || node.querySelector("pre")) {
                        shouldInit = true;
                    }
                }
            });
        });

        if (shouldInit) {
            initCodeCopyButtons();
        }
    });

    // Start observing when DOM is ready
    document.addEventListener("DOMContentLoaded", () => {
        observer.observe(document.body, {
            childList: true,
            subtree: true,
        });
    });
}

// Export for use in other modules
window.initCodeCopyButtons = initCodeCopyButtons;
