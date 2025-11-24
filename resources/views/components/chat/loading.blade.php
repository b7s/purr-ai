<div class="chat-row">
    <x-chat.avatar type="ai" />
    <div class="chat-bubble secondary">
        <div class="flex items-center justify-center py-2">
            <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 14 32 4" preserveAspectRatio="none">
                <style>
                    .loading-dot {
                        fill: rgb(75, 85, 99);
                    }

                    .dark .loading-dot {
                        fill: rgb(156, 163, 175);
                    }
                </style>
                <path class="loading-dot" opacity="0.8" transform="translate(0 0)" d="M2 14 V18 H6 V14z">
                    <animateTransform attributeName="transform" type="translate" values="0 0; 24 0; 0 0" dur="2s"
                        begin="0" repeatCount="indefinite" keySplines="0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8"
                        calcMode="spline" />
                </path>
                <path class="loading-dot" opacity="0.5" transform="translate(0 0)" d="M0 14 V18 H8 V14z">
                    <animateTransform attributeName="transform" type="translate" values="0 0; 24 0; 0 0" dur="2s"
                        begin="0.1s" repeatCount="indefinite" keySplines="0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8"
                        calcMode="spline" />
                </path>
                <path class="loading-dot" opacity="0.25" transform="translate(0 0)" d="M0 14 V18 H8 V14z">
                    <animateTransform attributeName="transform" type="translate" values="0 0; 24 0; 0 0" dur="2s"
                        begin="0.2s" repeatCount="indefinite" keySplines="0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8"
                        calcMode="spline" />
                </path>
            </svg>
        </div>
    </div>
</div>