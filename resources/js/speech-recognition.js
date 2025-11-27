/**
 * Local Speech Recognition for PurrAI
 * Records audio and sends to backend for transcription via whisper.cpp or AI API
 */

class LocalSpeechRecognition {
    constructor() {
        this.isRecording = false;
        this.isDiscarded = false;
        this.isProcessing = false;
        this.transcript = "";
        this.floatingDiv = null;
        this.mediaRecorder = null;
        this.audioChunks = [];
        this.audioStream = null;
        this.analyser = null;
        this.animationFrame = null;
        this.escapeHandler = null;
        this.silenceTimer = null;
        this.lastSoundTime = null;
        this.hasSoundDetected = false;
        this.silenceThreshold = 5000; // 5 seconds of silence
        this.soundThreshold = 2; // Minimum RMS level to consider as sound (2% of max volume)

        this.init();
    }

    init() {
        this.createFloatingInterface();
    }

    getFilterConfig(level) {
        const configs = {
            disabled: {
                smoothing: 0.3,
                minDecibels: -90,
                maxDecibels: -10,
                highPassFreq: 20,
                highPassQ: 0.1,
                lowPassFreq: 20000,
                lowPassQ: 0.1,
            },
            light: {
                smoothing: 0.5,
                minDecibels: -70,
                maxDecibels: -10,
                highPassFreq: 60,
                highPassQ: 0.5,
                lowPassFreq: 10000,
                lowPassQ: 0.5,
            },
            medium: {
                smoothing: 0.8,
                minDecibels: -60,
                maxDecibels: -10,
                highPassFreq: 80,
                highPassQ: 0.7,
                lowPassFreq: 8000,
                lowPassQ: 0.7,
            },
            high: {
                smoothing: 0.9,
                minDecibels: -50,
                maxDecibels: -10,
                highPassFreq: 100,
                highPassQ: 1.0,
                lowPassFreq: 6000,
                lowPassQ: 1.0,
            },
        };

        return configs[level] || configs.medium;
    }

    createFloatingInterface() {
        // Remove existing interface if present
        const existing = document.getElementById("speech-recording-interface");
        if (existing) {
            existing.remove();
        }

        this.floatingDiv = document.createElement("div");
        this.floatingDiv.id = "speech-recording-interface";
        this.floatingDiv.className = "speech-recording-interface hidden";

        // Get translations from global object
        const t = window.speechRecognitionTranslations || {
            settings: "Settings",
            audio_device: "AudioDevice",
            default_audio_device: "Default",
            auto_send: "Auto-send message",
        };

        this.floatingDiv.innerHTML = `
            <div class="speech-recording-content purrai-opacity-box">
                <div class="recording-controls">
                    <button type="button" class="btn-recording-action btn-trash">
                        <i class="iconoir-trash"></i>
                    </button>
                    <div class="wave-container">
                        <div class="wave-bar"></div>
                        <div class="wave-bar"></div>
                        <div class="wave-bar"></div>
                        <div class="wave-bar"></div>
                        <div class="wave-bar"></div>
                    </div>
                    <button type="button" class="btn-recording-action btn-stop">
                        <i class="iconoir-check"></i>
                    </button>
                </div>
                <div class="transcript-preview hidden"></div>

                <!-- Settings Accordion -->
                <div class="recording-settings-accordion">
                    <button type="button" class="accordion-toggle">
                        <i class="iconoir-settings"></i>
                        <span>${t.settings}</span>
                        <i class="iconoir-nav-arrow-down accordion-arrow"></i>
                    </button>
                    <div class="accordion-content hidden">
                        <div class="accordion-inner">
                            <div class="setting-row">
                                <label class="setting-label">${
                                    t.audio_device
                                }</label>
                                <select class="speech-select mic-select">
                                    <option value="default">${
                                        t.default_audio_device
                                    }</option>
                                </select>
                            </div>
                            <div class="setting-row speech-provider-row hidden">
                                <label class="setting-label">${
                                    t.speech_provider
                                }</label>
                                <select class="speech-select provider-select">
                                </select>
                            </div>
                            <div class="setting-row">
                                <label class="setting-label-inline cursor-pointer">
                                    <span>${
                                        t.auto_send || "Auto-send message"
                                    }</span>
                                    <input type="checkbox" class="auto-send-toggle" />
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(this.floatingDiv);

        // Click outside to discard
        this.floatingDiv.addEventListener("click", (e) => {
            if (e.target === this.floatingDiv) {
                this.discard();
            }
        });

        this.floatingDiv
            .querySelector(".btn-stop")
            .addEventListener("click", () => {
                if (!this.isProcessing) {
                    this.stop();
                }
            });
        this.floatingDiv
            .querySelector(".btn-trash")
            .addEventListener("click", () => {
                if (!this.isProcessing) {
                    this.discard();
                }
            });

        // Accordion toggle
        const accordionToggle =
            this.floatingDiv.querySelector(".accordion-toggle");
        const accordionContent =
            this.floatingDiv.querySelector(".accordion-content");
        const accordionArrow =
            this.floatingDiv.querySelector(".accordion-arrow");

        accordionToggle.addEventListener("click", () => {
            accordionContent.classList.toggle("hidden");
            accordionArrow.classList.toggle("rotate-180");
        });

        // AudioDevice select change
        const micSelect = this.floatingDiv.querySelector(".mic-select");
        micSelect.addEventListener("change", (e) => {
            const deviceId = e.target.value;
            if (window.audioDevicesManager) {
                window.audioDevicesManager.setSelectedDeviceId(deviceId);
            }
            // Restart recording with new audio_device
            if (this.isRecording) {
                this.restartWithNewDevice(deviceId);
            }
        });

        // Speech provider select change
        const providerSelect =
            this.floatingDiv.querySelector(".provider-select");
        providerSelect.addEventListener("change", async (e) => {
            const provider = e.target.value;
            await this.updateSpeechProvider(provider);
        });

        // Auto-send toggle change
        const autoSendToggle =
            this.floatingDiv.querySelector(".auto-send-toggle");
        autoSendToggle.addEventListener("change", async (e) => {
            const enabled = e.target.checked;
            await this.updateAutoSendSetting(enabled);
        });

        // Listen for device updates
        window.addEventListener("audio-devices-updated", (e) => {
            this.updateAudioDeviceSelect(e.detail.devices);
        });
    }

    updateAudioDeviceSelect(devices) {
        const micSelect = this.floatingDiv?.querySelector(".mic-select");
        if (!micSelect) return;

        const currentValue = micSelect.value;
        micSelect.innerHTML = "";

        devices.forEach((device) => {
            const option = document.createElement("option");
            option.value = device.id;
            option.textContent = device.label;
            micSelect.appendChild(option);
        });

        // Restore selection
        if (devices.some((d) => d.id === currentValue)) {
            micSelect.value = currentValue;
        } else if (window.audioDevicesManager) {
            micSelect.value = window.audioDevicesManager.getSelectedDeviceId();
        }
    }

    updateSpeechProviderSelect() {
        const providerSelect =
            this.floatingDiv?.querySelector(".provider-select");
        const providerRow = this.floatingDiv?.querySelector(
            ".speech-provider-row"
        );

        if (!providerSelect || !providerRow) return;

        const useLocal = window.useLocalSpeech ?? true;
        const options = window.speechProviderOptions ?? {};
        const selected = window.selectedSpeechProvider ?? "";

        // Show/hide provider select based on useLocalSpeech
        if (useLocal || Object.keys(options).length === 0) {
            providerRow.classList.add("hidden");
            return;
        }

        providerRow.classList.remove("hidden");
        providerSelect.innerHTML = "";

        // Add grouped options
        // Format: { "OpenAI": { "openai:model1": "Model 1", "openai:model2": "Model 2" } }
        Object.entries(options).forEach(([providerName, models]) => {
            const optgroup = document.createElement("optgroup");
            optgroup.label = providerName;

            Object.entries(models).forEach(([value, label]) => {
                const option = document.createElement("option");
                option.value = value;
                option.textContent = label;
                optgroup.appendChild(option);
            });

            providerSelect.appendChild(optgroup);
        });

        // Set selected value
        if (selected) {
            providerSelect.value = selected;
        }
    }

    updateAutoSendToggle() {
        const autoSendToggle =
            this.floatingDiv?.querySelector(".auto-send-toggle");
        if (!autoSendToggle) return;

        const enabled = window.autoSendAfterTranscription ?? false;
        autoSendToggle.checked = enabled;
    }

    async updateAutoSendSetting(enabled) {
        try {
            const csrfToken =
                document.querySelector('meta[name="csrf-token"]')?.content ||
                window.livewire?.csrf ||
                document.querySelector('input[name="_token"]')?.value;

            const response = await fetch("/api/update-auto-send-setting", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
                body: JSON.stringify({ enabled }),
            });

            if (!response.ok) {
                throw new Error("Failed to update auto-send setting");
            }

            window.autoSendAfterTranscription = enabled;
            window.toast?.success?.(
                enabled ? "Auto-send enabled" : "Auto-send disabled",
                2000
            );
        } catch (error) {
            console.error("Failed to update auto-send setting:", error);
            window.toast?.error?.("Failed to update setting", 3000);
        }
    }

    async updateSpeechProvider(provider) {
        try {
            const csrfToken =
                document.querySelector('meta[name="csrf-token"]')?.content ||
                window.livewire?.csrf ||
                document.querySelector('input[name="_token"]')?.value;

            const response = await fetch("/api/update-speech-provider", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
                body: JSON.stringify({ provider }),
            });

            if (!response.ok) {
                throw new Error("Failed to update speech provider");
            }

            window.selectedSpeechProvider = provider;
            window.toast?.success?.("Speech provider updated", 2000);
        } catch (error) {
            console.error("Failed to update speech provider:", error);
            window.toast?.error?.("Failed to update speech provider", 3000);
        }
    }

    async restartWithNewDevice(deviceId) {
        // Stop current recording without processing
        this.isDiscarded = true;
        this.audioChunks = [];

        if (this.mediaRecorder && this.mediaRecorder.state !== "inactive") {
            this.mediaRecorder.stop();
        }
        this.stopAudioCapture();

        // Small delay then restart
        await new Promise((resolve) => setTimeout(resolve, 100));
        this.isDiscarded = false;

        // Start new recording
        await this.startRecording();
    }

    syncSettingsUI() {
        if (!this.floatingDiv) return;

        const micSelect = this.floatingDiv.querySelector(".mic-select");

        if (micSelect && window.audioDevicesManager) {
            this.updateAudioDeviceSelect(
                window.audioDevicesManager.getDevices()
            );
            micSelect.value = window.audioDevicesManager.getSelectedDeviceId();
        }

        // Update speech provider select
        this.updateSpeechProviderSelect();

        // Update auto-send toggle
        this.updateAutoSendToggle();
    }

    showFloatingInterface() {
        // Check if floatingDiv exists in DOM, if not recreate it
        if (!this.floatingDiv || !document.body.contains(this.floatingDiv)) {
            this.createFloatingInterface();
        }

        if (this.floatingDiv) {
            this.floatingDiv.classList.remove("hidden");
            // Force reflow to ensure CSS transition works
            void this.floatingDiv.offsetHeight;

            // Add ESC key listener
            this.escapeHandler = (e) => {
                if (e.key === "Escape" && this.isRecording) {
                    this.discard();
                }
            };
            document.addEventListener("keydown", this.escapeHandler);
        } else {
            console.error("Floating div not found!");
        }
    }

    hideFloatingInterface() {
        if (this.floatingDiv) {
            this.floatingDiv.classList.add("hidden");

            // Reset accordion state
            const accordionContent =
                this.floatingDiv.querySelector(".accordion-content");
            const accordionArrow =
                this.floatingDiv.querySelector(".accordion-arrow");
            if (accordionContent) {
                accordionContent.classList.add("hidden");
            }
            if (accordionArrow) {
                accordionArrow.classList.remove("rotate-180");
            }
        }
        this.stopWaveAnimation();

        // Remove ESC key listener
        if (this.escapeHandler) {
            document.removeEventListener("keydown", this.escapeHandler);
            this.escapeHandler = null;
        }

        setTimeout(
            () => document.querySelector('[x-ref="messageInput"]')?.focus(),
            301
        );
    }

    updateTranscriptDisplay(text = null) {
        const preview = this.floatingDiv?.querySelector(".transcript-preview");
        if (preview) {
            const displayText = text || this.transcript || "";

            // Only show if there's actual transcript text (not status messages)
            const isStatusMessage = [
                "Recording...",
                "Listening...",
                "Processing...",
                "Requesting microphone access...",
                "Requesting audio device access...",
            ].includes(displayText);

            if (displayText.trim() && !isStatusMessage) {
                preview.textContent = displayText;
                preview.classList.remove("hidden");
                preview.style.fontStyle = "normal";
                preview.style.color = "";
            } else {
                preview.classList.add("hidden");
            }
        }
    }

    startWaveAnimation() {
        if (!this.analyser) return;

        const bars = this.floatingDiv?.querySelectorAll(".wave-bar");
        if (!bars || bars.length === 0) return;

        // Use time domain data (waveform) instead of frequency for better speech detection
        const bufferLength = this.analyser.fftSize;
        const dataArray = new Uint8Array(bufferLength);
        const frequencyData = new Uint8Array(this.analyser.frequencyBinCount);

        const centerIndex = Math.floor(bars.length / 2);
        const noiseThreshold = 30; // Minimum value to consider as actual sound

        const animate = () => {
            if (!this.isRecording) return;

            // Get waveform data for silence detection (more accurate for speech)
            this.analyser.getByteTimeDomainData(dataArray);

            // Calculate RMS (Root Mean Square) for better volume detection
            let sumSquares = 0;
            for (let i = 0; i < bufferLength; i++) {
                const normalized = (dataArray[i] - 128) / 128; // Normalize to -1 to 1
                sumSquares += normalized * normalized;
            }
            const rms = Math.sqrt(sumSquares / bufferLength);
            const volumeLevel = rms * 100; // Scale to 0-100

            // Detect if there's actual sound (above threshold)
            if (volumeLevel > this.soundThreshold) {
                this.lastSoundTime = Date.now();
                if (!this.hasSoundDetected) {
                    this.hasSoundDetected = true;
                }
                this.resetSilenceTimer();
            }

            // Get frequency data for visualization
            this.analyser.getByteFrequencyData(frequencyData);

            // Use RMS-based volume (already calculated above) for visualization
            // Scale it up significantly for better visual feedback
            const visualVolume = Math.min(255, rms * 1200);

            // Current time for wave effect
            const time = Date.now() * 0.01;

            bars.forEach((bar, index) => {
                // Create wave effect: each bar has a phase offset
                const phase = (index - centerIndex) * 0.8;
                const waveOffset = Math.sin(time + phase) * 0.4 + 0.6;

                // Distance from center affects base amplitude
                const distanceFromCenter = Math.abs(index - centerIndex);
                const centerBoost =
                    1 - (distanceFromCenter / centerIndex) * 0.2;

                // Combine volume with wave effect
                const value = visualVolume * waveOffset * centerBoost;
                const height = Math.max(6, (value / 255) * 80);

                bar.style.height = `${height}px`;
                bar.style.opacity =
                    visualVolume > 5 ? 0.4 + (value / 255) * 0.6 : 0.2;
            });

            this.animationFrame = requestAnimationFrame(animate);
        };

        animate();
    }

    stopWaveAnimation() {
        if (this.animationFrame) {
            cancelAnimationFrame(this.animationFrame);
            this.animationFrame = null;
        }

        // Clear silence timer
        this.clearSilenceTimer();

        // Reset bars
        const bars = this.floatingDiv?.querySelectorAll(".wave-bar");
        bars?.forEach((bar) => {
            bar.style.height = "";
            bar.style.opacity = "";
        });
    }

    resetSilenceTimer() {
        // Clear existing timer
        this.clearSilenceTimer();

        // Only start timer if we've detected sound before
        if (!this.hasSoundDetected) {
            return;
        }

        // Start new timer
        this.silenceTimer = setTimeout(() => {
            if (this.isRecording && this.hasSoundDetected) {
                this.updateTranscriptDisplay("Silence detected, processing...");
                this.stop();
            }
        }, this.silenceThreshold);
    }

    clearSilenceTimer() {
        if (this.silenceTimer) {
            clearTimeout(this.silenceTimer);
            this.silenceTimer = null;
        }
    }

    async start() {
        if (this.isRecording) {
            return;
        }

        // Ensure clean state before starting (but don't hide interface)
        this.isRecording = false;
        this.isDiscarded = false;
        this.transcript = "";
        this.audioChunks = [];
        this.hasSoundDetected = false;
        this.lastSoundTime = null;
        this.lastLogTime = null;
        this.clearSilenceTimer();
        this.stopWaveAnimation();

        // Stop any existing audio capture
        if (this.mediaRecorder && this.mediaRecorder.state !== "inactive") {
            try {
                this.mediaRecorder.stop();
            } catch (e) {
                // MediaRecorder already stopped
            }
        }
        this.stopAudioCapture();

        // Small delay to ensure cleanup is complete
        await new Promise((resolve) => setTimeout(resolve, 100));

        // Show interface immediately to provide feedback
        this.showFloatingInterface();
        this.syncSettingsUI();
        this.updateTranscriptDisplay("Requesting audio_device access...");

        await this.startRecording();
    }

    async startRecording() {
        try {
            // Get audio constraints with selected device
            const audioConstraints = await this.getAudioConstraints();

            this.audioStream = await navigator.mediaDevices.getUserMedia({
                audio: audioConstraints,
            });

            // Setup audio analyser for wave animation with noise filtering
            const audioContext = new (window.AudioContext ||
                window.webkitAudioContext)();
            const source = audioContext.createMediaStreamSource(
                this.audioStream
            );

            // Get noise suppression level from settings
            const noiseLevel = window.noiseSuppressionLevel || "medium";

            // Configure filters based on noise suppression level
            const filterConfig = this.getFilterConfig(noiseLevel);

            // Create noise gate filter
            this.analyser = audioContext.createAnalyser();
            this.analyser.fftSize = 512;
            this.analyser.smoothingTimeConstant = filterConfig.smoothing;
            this.analyser.minDecibels = filterConfig.minDecibels;
            this.analyser.maxDecibels = filterConfig.maxDecibels;

            // Add high-pass filter to remove low-frequency noise
            const highPassFilter = audioContext.createBiquadFilter();
            highPassFilter.type = "highpass";
            highPassFilter.frequency.value = filterConfig.highPassFreq;
            highPassFilter.Q.value = filterConfig.highPassQ;

            // Add low-pass filter to remove high-frequency noise
            const lowPassFilter = audioContext.createBiquadFilter();
            lowPassFilter.type = "lowpass";
            lowPassFilter.frequency.value = filterConfig.lowPassFreq;
            lowPassFilter.Q.value = filterConfig.lowPassQ;

            // Connect audio processing chain
            source.connect(highPassFilter);
            highPassFilter.connect(lowPassFilter);
            lowPassFilter.connect(this.analyser);

            // Setup media recorder
            this.mediaRecorder = new MediaRecorder(this.audioStream, {
                mimeType: this.getSupportedMimeType(),
            });

            this.audioChunks = [];

            this.mediaRecorder.ondataavailable = (event) => {
                if (event.data.size > 0) {
                    this.audioChunks.push(event.data);
                }
            };

            this.mediaRecorder.onstop = async () => {
                await this.processAudio();
            };

            this.mediaRecorder.start(100); // Collect data every 100ms
            this.isRecording = true;
            this.isDiscarded = false;
            this.transcript = "";
            this.hasSoundDetected = false;
            this.lastSoundTime = null;
            this.updateTranscriptDisplay("Recording...");
            this.startWaveAnimation();
        } catch (error) {
            console.error("Failed to start recording:", error);
            this.hideFloatingInterface();
            window.toast.error(
                "Failed to access audio_device. Please check your permissions."
            );
        }
    }

    getSupportedMimeType() {
        // Prefer formats that are more compatible with OpenAI
        const types = [
            "audio/mp4",
            "audio/mpeg",
            "audio/webm;codecs=opus",
            "audio/webm",
            "audio/ogg;codecs=opus",
        ];

        for (const type of types) {
            if (MediaRecorder.isTypeSupported(type)) {
                return type;
            }
        }

        return "audio/webm";
    }

    async getAudioConstraints() {
        const constraints = {
            echoCancellation: { ideal: true },
            noiseSuppression: { ideal: true },
            autoGainControl: { ideal: true },
            sampleRate: { ideal: 16000 },
            // More aggressive noise filtering
            channelCount: { ideal: 1 },
            latency: { ideal: 0 },
            // Advanced noise suppression
            googEchoCancellation: { ideal: true },
            googAutoGainControl: { ideal: true },
            googNoiseSuppression: { ideal: true },
            googHighpassFilter: { ideal: true },
            googTypingNoiseDetection: { ideal: true },
            googAudioMirroring: { ideal: false },
        };

        // Get selected device from manager
        if (window.audioDevicesManager) {
            const deviceId = window.audioDevicesManager.getSelectedDeviceId();
            if (deviceId && deviceId !== "default") {
                constraints.deviceId = { exact: deviceId };
            }
        }

        return constraints;
    }

    async processAudio() {
        // Check if recording was discarded
        if (this.isDiscarded) {
            this.isDiscarded = false;
            return;
        }

        if (this.audioChunks.length === 0) {
            this.hideFloatingInterface();
            return;
        }

        this.isProcessing = true;
        this.setButtonLoading(true);
        this.updateTranscriptDisplay("Processing...");

        try {
            const audioBlob = new Blob(this.audioChunks, {
                type: this.getSupportedMimeType(),
            });

            // Send to backend for transcription
            const formData = new FormData();
            formData.append("audio", audioBlob, "recording.webm");

            // Get CSRF token from meta tag or Livewire
            const csrfToken =
                document.querySelector('meta[name="csrf-token"]')?.content ||
                window.livewire?.csrf ||
                document.querySelector('input[name="_token"]')?.value;

            const response = await fetch("/api/transcribe", {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
            });

            const data = await response.json();

            if (!response.ok) {
                this.isProcessing = false;
                this.setButtonLoading(false);
                this.hideFloatingInterface();
                if (response.status === 503) {
                    window.toast.warning(
                        "Setup required. Please check Settings.",
                        4000
                    );
                } else {
                    window.toast.error(
                        data.error || "Transcription failed",
                        4000
                    );
                }
                return;
            }

            this.transcript = data.text || "";

            if (this.transcript.trim()) {
                this.sendToTextarea(this.transcript.trim());
                window.toast.success("Transcription completed", 2000);
            } else {
                this.isProcessing = false;
                this.setButtonLoading(false);
                this.hideFloatingInterface();
                window.toast.warning("No speech detected", 2000);
                return;
            }
        } catch (error) {
            console.error("Failed to process audio:", error);
            this.isProcessing = false;
            this.setButtonLoading(false);
            this.hideFloatingInterface();
            window.toast.error("Connection failed. Please try again.", 3000);
            return;
        }

        this.isProcessing = false;
        this.setButtonLoading(false);
        this.hideFloatingInterface();
    }

    stop() {
        this.isRecording = false;
        this.clearSilenceTimer();

        if (this.mediaRecorder && this.mediaRecorder.state !== "inactive") {
            this.mediaRecorder.stop();
        }

        this.stopAudioCapture();
    }

    discard() {
        this.isRecording = false;
        this.isDiscarded = true;
        this.transcript = "";
        this.audioChunks = [];
        this.hasSoundDetected = false;
        this.lastSoundTime = null;
        this.clearSilenceTimer();

        if (this.mediaRecorder && this.mediaRecorder.state !== "inactive") {
            this.mediaRecorder.stop();
        }

        this.stopAudioCapture();
        this.hideFloatingInterface();
    }

    stopAudioCapture() {
        if (this.audioStream) {
            this.audioStream.getTracks().forEach((track) => track.stop());
            this.audioStream = null;
        }
        this.analyser = null;
        this.mediaRecorder = null;
    }

    setButtonLoading(loading) {
        const stopBtn = this.floatingDiv?.querySelector(".btn-stop");
        const trashBtn = this.floatingDiv?.querySelector(".btn-trash");

        if (stopBtn) {
            const icon = stopBtn.querySelector("i");
            if (loading) {
                stopBtn.disabled = true;
                stopBtn.classList.add("opacity-50", "cursor-not-allowed");
                if (icon) {
                    icon.className = "iconoir-refresh animate-spin";
                }
            } else {
                stopBtn.disabled = false;
                stopBtn.classList.remove("opacity-50", "cursor-not-allowed");
                if (icon) {
                    icon.className = "iconoir-check";
                }
            }
        }

        if (trashBtn) {
            if (loading) {
                trashBtn.disabled = true;
                trashBtn.classList.add("opacity-50", "cursor-not-allowed");
            } else {
                trashBtn.disabled = false;
                trashBtn.classList.remove("opacity-50", "cursor-not-allowed");
            }
        }
    }

    forceCleanup() {
        this.isRecording = false;
        this.isProcessing = false;
        this.isDiscarded = true;
        this.transcript = "";
        this.audioChunks = [];
        this.hasSoundDetected = false;
        this.lastSoundTime = null;
        this.lastLogTime = null;
        this.clearSilenceTimer();
        this.stopWaveAnimation();
        this.setButtonLoading(false);

        if (this.mediaRecorder && this.mediaRecorder.state !== "inactive") {
            try {
                this.mediaRecorder.stop();
            } catch (e) {
                // MediaRecorder already stopped
            }
        }

        this.stopAudioCapture();
        this.hideFloatingInterface();

        // Reset discarded flag after cleanup
        this.isDiscarded = false;
    }

    sendToTextarea(text) {
        const textarea = document.querySelector(".input-field");
        if (textarea) {
            const currentValue = textarea.value.trim();
            textarea.value = currentValue ? `${currentValue} ${text}` : text;

            textarea.dispatchEvent(new Event("input", { bubbles: true }));
            textarea.focus();

            // Auto-send if enabled
            if (window.autoSendAfterTranscription) {
                // Delay to ensure textarea value is synced with Livewire
                setTimeout(() => {
                    const sendButton =
                        document.getElementById("send-message-btn");
                    if (
                        sendButton &&
                        !sendButton.disabled &&
                        textarea.value.trim()
                    ) {
                        sendButton.click();
                    }
                }, 600);
            }
        }
    }
}

// Initialize and expose globally
let speechRecognition = null;

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initSpeechRecognition);
} else {
    initSpeechRecognition();
}

function initSpeechRecognition() {
    // If already exists, force cleanup before reinitializing
    if (window.speechRecognition) {
        window.speechRecognition.forceCleanup();
    }

    speechRecognition = new LocalSpeechRecognition();
    window.speechRecognition = speechRecognition;

    attachAudioDeviceButton();

    // Handle Livewire navigation - cleanup and reattach
    document.addEventListener("livewire:navigating", () => {
        if (window.speechRecognition) {
            window.speechRecognition.forceCleanup();
        }
    });

    document.addEventListener("livewire:navigated", () => {
        // Recreate interface after navigation (DOM was replaced)
        if (window.speechRecognition) {
            window.speechRecognition.createFloatingInterface();
        }
        // Small delay to ensure DOM is ready
        setTimeout(attachAudioDeviceButton, 150);
    });

    // Handle page visibility change (tab switch, minimize)
    document.addEventListener("visibilitychange", () => {
        if (
            document.hidden &&
            window.speechRecognition &&
            window.speechRecognition.isRecording
        ) {
            window.speechRecognition.forceCleanup();
        }
    });

    // Handle page unload
    window.addEventListener("beforeunload", () => {
        if (window.speechRecognition) {
            window.speechRecognition.forceCleanup();
        }
    });
}

function attachAudioDeviceButton() {
    const micButton = document.getElementById("audio_device-button");

    if (micButton && !micButton.dataset.speechAttached) {
        micButton.dataset.speechAttached = "true";

        micButton.addEventListener("click", async (e) => {
            e.preventDefault();

            if (!window.speechRecognition) {
                console.error("Speech recognition not initialized");
                window.toast.error("Speech recognition is not available");
                return;
            }

            if (window.speechRecognition.isRecording) {
                return;
            }

            try {
                const isValid = await validateSpeechConfiguration();

                if (isValid) {
                    await window.speechRecognition.start();
                }
            } catch (error) {
                console.error("Failed to start speech recognition:", error);
                window.toast.error(
                    "Failed to start speech recognition. Please try again."
                );
            }
        });
    }
}

async function validateSpeechConfiguration() {
    try {
        // Get CSRF token from meta tag or Livewire
        const csrfToken =
            document.querySelector('meta[name="csrf-token"]')?.content ||
            window.livewire?.csrf ||
            document.querySelector('input[name="_token"]')?.value;

        const response = await fetch("/api/validate-speech-config", {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                Accept: "application/json",
            },
        });

        if (!response.ok) {
            console.error("Validation request failed:", response.status);
            throw new Error("Failed to validate configuration");
        }

        const data = await response.json();

        if (!data.valid) {
            // Redirect to settings with anchor
            window.location.href =
                "/settings#active-speech-recognition-settings";
            return false;
        }

        return true;
    } catch (error) {
        console.error("Failed to validate speech configuration:", error);
        // On error, redirect to settings
        window.location.href = "/settings#active-speech-recognition-settings";
        return false;
    }
}
