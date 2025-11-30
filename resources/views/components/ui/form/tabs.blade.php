@props(['tabs', 'active' => null])

<div x-data="{ activeTab: '{{ $active ?? request()->input('tab') ?? array_key_first($tabs) }}' }">
    <div class="settings-tabs card">
        @foreach($tabs as $key => $tab)
            <button type="button" @click="activeTab = '{{ $key }}'"
                :class="activeTab === '{{ $key }}' ? 'settings-tab-active' : 'settings-tab-inactive'" class="settings-tab">
                @if(isset($tab['icon']))
                    <i class="iconoir-{{ $tab['icon'] }}"></i>
                @endif
                {{ $tab['label'] }}
            </button>
        @endforeach
    </div>

    {{ $slot }}
</div>