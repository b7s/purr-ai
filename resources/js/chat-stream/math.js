import katex from "katex";
import "katex/dist/katex.min.css";

const katexOptions = {
    delimiters: [
        { left: "$$", right: "$$", display: true },
        { left: "\\[", right: "\\]", display: true },
        { left: "$", right: "$", display: false },
        { left: "\\(", right: "\\)", display: false },
    ],
    throwOnError: false,
    output: "html",
};

function escapeHtml(value) {
    return value
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");
}

let mathRenderScheduled = false;

function scheduleMathRender(targetElement = null) {
    if (mathRenderScheduled) {
        return;
    }

    mathRenderScheduled = true;
    requestAnimationFrame(() => {
        const container =
            targetElement || document.getElementById("messages-container");
        if (container) {
            renderMathInElement(container);
        }
        mathRenderScheduled = false;
    });
}

function initMathObserver() {
    const container = document.getElementById("messages-container");
    if (!container || container.__mathObserverInitialized) {
        return;
    }

    const observer = new MutationObserver((mutations) => {
        const needsRender = mutations.some((mutation) => {
            if (mutation.type === "childList") {
                return true;
            }

            if (
                mutation.type === "attributes" &&
                mutation.target?.classList?.contains("math")
            ) {
                return true;
            }

            return false;
        });

        if (needsRender) {
            scheduleMathRender(container);
        }
    });

    observer.observe(container, {
        childList: true,
        subtree: true,
        attributes: true,
    });

    container.__mathObserverInitialized = true;
}

function protectMathExpressions(content) {
    const mathExpressions = [];
    let processedContent = content;

    const addExpression = (tex, displayMode) => {
        const id = `MATH_${mathExpressions.length}_${
            displayMode ? "BLOCK" : "INLINE"
        }`;
        mathExpressions.push({ id, tex: tex.trim(), displayMode });
        return id;
    };

    processedContent = processedContent.replace(
        /\\\[([\s\S]+?)\\\]/g,
        (_, expr) => {
            const id = addExpression(expr, true);
            return ` ${id} `;
        }
    );

    processedContent = processedContent.replace(
        /\$\$([\s\S]+?)\$\$/g,
        (_, expr) => {
            const id = addExpression(expr, true);
            return ` ${id} `;
        }
    );

    processedContent = processedContent.replace(
        /\\\(([\s\S]+?)\\\)/g,
        (_, expr) => {
            const id = addExpression(expr, false);
            return ` ${id} `;
        }
    );

    processedContent = processedContent.replace(
        /(^|[^\\])\$([^$\n]+?)\$/g,
        (match, prefix, expr) => {
            if (prefix === "\\") {
                return match;
            }

            const id = addExpression(expr, false);
            return `${prefix}${id}`;
        }
    );

    return { protectedContent: processedContent, mathExpressions };
}

function restoreMathExpressions(html, mathExpressions) {
    if (!mathExpressions || mathExpressions.length === 0) {
        return html;
    }

    let result = html;
    mathExpressions.forEach(({ id, tex, displayMode }) => {
        const mathClass = displayMode ? "math display-math" : "math";
        const safeTex = escapeHtml(tex);
        const encodedTex = encodeURIComponent(tex);

        result = result.replace(
            new RegExp(`\\s*${id}\\s*`, "g"),
            ` <span class="${mathClass}" data-tex="${encodedTex}">${safeTex}</span> `
        );
    });

    return result;
}

function renderMathInElement(element) {
    if (!element || !katex) {
        return;
    }

    const mathElements = element.getElementsByClassName("math");
    Array.from(mathElements).forEach((mathElement) => {
        const storedTex = mathElement.getAttribute("data-tex");
        const texContent = storedTex
            ? decodeURIComponent(storedTex)
            : mathElement.getAttribute("data-katex-original") ||
              mathElement.textContent.trim();

        const displayMode = mathElement.classList.contains("display-math");

        if (!texContent) {
            return;
        }

        try {
            katex.render(texContent, mathElement, {
                ...katexOptions,
                displayMode,
                throwOnError: false,
                output: "html",
                strict: false,
            });

            mathElement.setAttribute(
                "data-katex-original",
                encodeURIComponent(texContent)
            );
        } catch (error) {
            console.warn("KaTeX render error:", error);
            mathElement.classList.add("text-red-500");
        }
    });
}

export {
    initMathObserver,
    protectMathExpressions,
    renderMathInElement,
    restoreMathExpressions,
    scheduleMathRender,
};
