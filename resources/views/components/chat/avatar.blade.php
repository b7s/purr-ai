@props(['type' => 'ai'])

@if($type === 'ai')
    <div class="purr-ai-logo">
        <img src="/storage/images/logo-PurrAI-256.webp" alt="PurrAI" class="w-full h-full">
    </div>
@else
    <div
        class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-200 to-blue-400 dark:from-blue-700 dark:to-blue-500 flex items-center justify-center shadow-inner shrink-0">
        <i class="iconoir-user text-sm text-white"></i>
    </div>
@endif