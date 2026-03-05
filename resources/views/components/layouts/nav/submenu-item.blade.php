@props(['item', 'itemKey'])

@canAccess($item['acl'] ?? null)
<li class="{{ $item['class'] ?? '' }}">
    <a
        class="menu-dropdown-item group"
        title="{{ synav()->translate($item['name']) }}"
        @isset($item['sub'])
            @click.prevent="selected = selected === '{{ $itemKey }}' ? '' : '{{ $itemKey }}'"
        @else
            href="{{ backend_route($item['route']) }}"
        @endisset
        :class="selected === '{{ $itemKey }}' || selected.startsWith('{{ $itemKey }}.') ? 'menu-dropdown-item-active' :
            'menu-dropdown-item-inactive'"
        wire:navigate
    >
        @if (!empty($item['icon']))
            <span class="{{ $item['icon'] }}"></span>
        @endif
        {{ synav()->translate($item['name']) }}

        @isset($item['sub'])
            <x-synapps::layouts.nav.arrow :itemKey="$itemKey" />
        @endisset
    </a>

    @isset($item['sub'])
        <x-synapps::layouts.nav.submenu
            :items="$item['sub']"
            :parentKey="$itemKey"
        />
    @endisset
</li>
@endcanAccess
