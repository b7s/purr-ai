/**
 * Audio Devices Manager for PurrAI
 * Handles audio_device selection and system audio capture
 * Compatible with Chromium and Electron
 */

class AudioDevicesManager {
    constructor() {
        this.devices = [];
        this.selectedDeviceId =
            localStorage.getItem("selectedAudioDeviceId") || "default";
        this.initialized = false;
    }

    async init() {
        if (this.initialized) return;

        try {
            // Request permission first to get device labels
            await navigator.mediaDevices.getUserMedia({ audio: true });
            await this.refreshDevices();
            this.initialized = true;

            // Listen for device changes
            navigator.mediaDevices.addEventListener("devicechange", () => {
                this.refreshDevices();
            });
        } catch (error) {
            console.error("Failed to initialize audio devices:", error);
        }
    }

    async refreshDevices() {
        try {
            const allDevices = await navigator.mediaDevices.enumerateDevices();
            this.devices = allDevices
                .filter((device) => device.kind === "audioinput")
                .map((device) => ({
                    id: device.deviceId,
                    label:
                        device.label ||
                        `AudioDevice ${device.deviceId.slice(0, 8)}`,
                    isDefault: device.deviceId === "default",
                }));

            // Dispatch event for UI updates
            window.dispatchEvent(
                new CustomEvent("audio-devices-updated", {
                    detail: { devices: this.devices },
                })
            );

            return this.devices;
        } catch (error) {
            console.error("Failed to enumerate devices:", error);
            return [];
        }
    }

    getDevices() {
        return this.devices;
    }

    getSelectedDeviceId() {
        return this.selectedDeviceId;
    }

    setSelectedDeviceId(deviceId) {
        this.selectedDeviceId = deviceId;
        localStorage.setItem("selectedAudioDeviceId", deviceId);
        this.saveToServer("selected_audio_device_id", deviceId);

        window.dispatchEvent(
            new CustomEvent("audio_device-changed", {
                detail: { deviceId },
            })
        );
    }

    saveToServer(key, value) {
        window.dispatchEvent(
            new CustomEvent("system-audio-changed", {
                detail: { enabled },
            })
        );
    }

    async saveToServer(key, value) {
        try {
            const csrfToken =
                document.querySelector('meta[name="csrf-token"]')?.content ||
                document.querySelector('input[name="_token"]')?.value;

            await fetch("/api/settings", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
                body: JSON.stringify({ key, value }),
            });
        } catch (error) {
            console.error("Failed to save setting:", error);
        }
    }

    async getAudioConstraints() {
        const constraints = {
            echoCancellation: true,
            noiseSuppression: true,
            autoGainControl: true,
            sampleRate: 16000,
        };

        if (this.selectedDeviceId && this.selectedDeviceId !== "default") {
            constraints.deviceId = { exact: this.selectedDeviceId };
        }

        return constraints;
    }

    /**
     * Check if system audio capture is supported
     * Note: This requires special permissions in Electron
     */
    isSystemAudioSupported() {
        // System audio capture is typically only available in Electron with specific setup
        return (
            typeof window.electron !== "undefined" ||
            navigator.mediaDevices.getDisplayMedia !== undefined
        );
    }
}

// Initialize and expose globally
window.audioDevicesManager = new AudioDevicesManager();

// Initialize when DOM is ready
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
        window.audioDevicesManager.init();
    });
} else {
    window.audioDevicesManager.init();
}

// Alpine.js component for audio_device selector
document.addEventListener("alpine:init", () => {
    Alpine.data("audio_deviceSelector", () => ({
        devices: [],
        selectedDeviceId: "default",
        loading: true,

        async init() {
            await window.audioDevicesManager.init();
            this.devices = window.audioDevicesManager.getDevices();
            await this.loadSelectedDevice();
            this.loading = false;

            window.addEventListener("audio-devices-updated", (e) => {
                this.devices = e.detail.devices;
            });
        },

        async loadSelectedDevice() {
            try {
                const csrfToken =
                    document.querySelector('meta[name="csrf-token"]')
                        ?.content ||
                    document.querySelector('input[name="_token"]')?.value;

                const response = await fetch(
                    "/api/settings/selected_audio_device_id",
                    {
                        headers: {
                            "X-CSRF-TOKEN": csrfToken,
                            Accept: "application/json",
                        },
                    }
                );

                if (response.ok) {
                    const data = await response.json();
                    if (data.value) {
                        this.selectedDeviceId = data.value;
                        window.audioDevicesManager.selectedDeviceId =
                            data.value;
                    }
                }
            } catch (error) {
                console.error("Failed to load selected device:", error);
                this.selectedDeviceId =
                    localStorage.getItem("selectedAudioDeviceId") || "default";
            }
        },

        selectDevice(deviceId) {
            this.selectedDeviceId = deviceId;
            window.audioDevicesManager.setSelectedDeviceId(deviceId);
        },

        async refreshDevices() {
            this.loading = true;
            this.devices = await window.audioDevicesManager.refreshDevices();
            this.loading = false;
        },
    }));
});
