@props(['type' => 'ai'])

@if ($type === 'ai')
    <div class="purr-ai-logo sticky top-2 transition-transform duration-200 hover:scale-110">
        <img
            src="{{ asset('images/mascot/logo.svg') }}"
            alt="PurrAI"
            class="w-full h-full"
        >
    </div>
@else
    <div class="user-avatar">
        @if (getUserName())
            <span class="text-white font-medium text-sm cursor-default uppercase">{{ getUserName()[0] }}</span>
        @else
            <i class="iconoir-user text-sm text-white"></i>
        @endif
    </div>
@endif
