@props(['title' => null, 'message' => null])

@php
    $title ??= __('chat.welcome_title');
    $message ??= __('chat.welcome_message');
    $mascotName = \App\Models\Setting::get('mascot_name', config('app.name'));
@endphp

<div class="welcome-container select-none">
    <div class="purr-ai-logo welcome-logo animate-welcome-logo">
        <img
            src="{{ asset('images/mascot/position-out-of-screen.webp') }}"
            alt="{{ config('app.name') }}"
            class="w-full h-full"
        >
    </div>
    <div>
        <h2 class="welcome-title text-slate-500 dark:text-slate-400">
            @php
                $user_name = \App\Models\Setting::get('user_name');
            @endphp
            @if ($user_name)
                {!! __('chat.welcome_title_back', [
                    'name' => "<span class='text-slate-800 dark:text-slate-100'>$mascotName</span>",
                ]) !!},
                <span class="border-b-3 text-slate-800 dark:text-slate-100 border-amber-400 hover:border-slate-100 hover:dark:border-slate-400">{{ explode(' ', $user_name)[0] }}</span>!
            @else
                {{ $title }}
            @endif
        </h2>
        <p class="welcome-message">
            {{ $message }}
        </p>
    </div>
</div>
