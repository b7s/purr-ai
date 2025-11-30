<svg
    class="w-8 h-8 inline-block"
    viewBox="0 0 24 24"
    xmlns="http://www.w3.org/2000/svg"
>
    <style>
        @keyframes spinner-pulse {

            0%,
            100% {
                opacity: 0.2;
                transform: scale(0.8);
            }

            50% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .spinner-dot {
            fill: currentColor;
            animation: spinner-pulse 1.2s ease-in-out infinite;
        }

        .spinner-dot:nth-child(1) {
            animation-delay: 0s;
        }

        .spinner-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .spinner-dot:nth-child(3) {
            animation-delay: 0.4s;
        }
    </style>
    <circle
        class="spinner-dot text-slate-900 dark:text-white"
        cx="4"
        cy="12"
        r="3"
    />
    <circle
        class="spinner-dot text-slate-900 dark:text-white"
        cx="12"
        cy="12"
        r="3"
    />
    <circle
        class="spinner-dot text-slate-900 dark:text-white"
        cx="20"
        cy="12"
        r="3"
    />
</svg>
