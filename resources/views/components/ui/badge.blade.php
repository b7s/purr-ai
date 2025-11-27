@props([
    'class',
])

<span {{ $attributes->merge(['class' => "px-2 py-1 rounded-lg bg-slate-500 text-white text-center inline-block"]) }}>
    {{ $slot }}
</span>