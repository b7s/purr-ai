@props(['title' => null, 'message' => null])

@php
    $mascotName = \App\Models\Setting::get('mascot_name', config('app.name'));

    $title ??= __('chat.welcome_title', ['name' => $mascotName]);
    $message ??= __('chat.welcome_message');

    $greetings = [
        'morning' => __('chat.greetings.morning'),
        'afternoon' => __('chat.greetings.afternoon'),
        'evening' => __('chat.greetings.evening'),
        'night' => __('chat.greetings.night'),
    ];
@endphp

<div 
    class="welcome-container select-none"
    x-data="{
        timeOfDay: 'morning',
        greetings: @js($greetings),
        updateGreeting() {
            const hour = new Date().getHours();
            if (hour < 12) this.timeOfDay = 'morning';
            else if (hour < 18) this.timeOfDay = 'afternoon';
            else if (hour < 22) this.timeOfDay = 'evening';
            else this.timeOfDay = 'night';
        },
        get greeting() {
            return this.greetings[this.timeOfDay] || this.greetings.morning;
        }
    }"
    x-init="updateGreeting();">
    <div class="purr-ai-logo welcome-logo animate-welcome-logo">
        <img
            src="{{ asset('images/mascot/position-out-of-screen.webp') }}"
            alt="{{ config('app.name') }}"
            class="w-full h-full"
        >
    </div>
    <div>
        <h2 class="welcome-title text-slate-600 dark:text-slate-400">
            @php
                $user_name = \App\Models\Setting::get('user_name');
            @endphp
            @if ($user_name)
                <span x-text="greeting"></span>,
                <span class="border-b-3 text-slate-800 dark:text-slate-100 border-amber-400 hover:border-slate-100 hover:dark:border-slate-400">{{ explode(' ', $user_name)[0] }}</span>!
                <div class="text-xl py-2">
                    {!! __('chat.welcome_title_back', [
                    'name' => "<span class='text-slate-800 dark:text-slate-100'>$mascotName</span>",
                ]) !!}.
                </div>
            @else
                <span x-text="greeting"></span>! {{ $title }}
            @endif
        </h2>
        <p class="welcome-message">
            {{ $message }}
        </p>
    </div>
</div>
