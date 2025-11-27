/**
 * Toast Helper Functions
 * Provides easy-to-use functions to show toast notifications
 */

window.showToast = function (message, type = "info", duration = 3000) {
    window.dispatchEvent(
        new CustomEvent("show-toast", {
            detail: { message, type, duration },
        })
    );
};

window.toast = {
    info: (message, duration = 3000) => showToast(message, "info", duration),
    success: (message, duration = 3000) =>
        showToast(message, "success", duration),
    warning: (message, duration = 3000) =>
        showToast(message, "warning", duration),
    error: (message, duration = 3000) => showToast(message, "error", duration),
};
