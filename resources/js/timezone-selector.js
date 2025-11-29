document.addEventListener("alpine:init", () => {
    Alpine.data("timezoneSelector", () => ({
        init() {
            // Auto-detect timezone if field is empty
            const input = this.$refs.timezoneInput;
            if (input && !input.value) {
                this.detectTimezone();
            }
        },

        detectTimezone() {
            try {
                const timezone =
                    Intl.DateTimeFormat().resolvedOptions().timeZone;
                if (timezone) {
                    this.$wire.set("timezone", timezone);
                }
            } catch (error) {
                console.error("Failed to detect timezone:", error);
            }
        },
    }));
});
