@props(['title' => '', 'message' => ''])

<div class="welcome-container select-none">
    <div class="purr-ai-logo welcome-logo">
        <img src="{{ asset('images/logo-PurrAI-256.webp') }}" alt="{{ config('app.name') }}" class="w-full h-full">
    </div>
    <h2 class="welcome-title">
        {{ $title }}
    </h2>
    <p class="welcome-message">
        {{ $message }}
    </p>
</div>