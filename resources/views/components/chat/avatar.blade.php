@props(['type' => 'ai'])

@if($type === 'ai')
    <div class="purr-ai-logo">
        <img src="{{ asset('images/mascot/logo.svg') }}" alt="PurrAI" class="w-full h-full">
    </div>
@else
    <div
        class="w-8 h-8 rounded-full bg-linear-to-tr from-slate-200 to-slate-400 dark:from-slate-700 dark:to-slate-500 flex items-center justify-center shadow-inner shrink-0 select-none">
        @if (getUserName())
            <span class="text-white font-medium text-sm cursor-default uppercase">{{ getUserName()[0] }}</span>
        @else
            <i class="iconoir-user text-sm text-white"></i>
        @endif
    </div>
@endif