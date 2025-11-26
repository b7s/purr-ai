@props(['name'])

<div x-show="activeTab === '{{ $name }}'" x-transition class="space-y-6">
    {{ $slot }}
</div>