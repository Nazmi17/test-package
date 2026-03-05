@props(['item', 'itemKey'])

@canAccess($item['acl'] ?? null)
<li class="{{ $item['class'] ?? '' }}">
    <a
        class="menu-item {{ synav()->is($itemKey) ? 'menu-item-active' : 'menu-item-inactive' }} group"
        title="{{ synav()->translate($item['name']) }}"
        @isset($item['sub'])
            @click.prevent="selected = selected === '{{ $itemKey }}' ? '' : '{{ $itemKey }}'"
        @else
            href="{{ backend_route($item['route']) }}"
        @endisset
        wire:navigate
    >
        @if (!empty($item['icon']))
            <span
                class="{{ $item['icon'] . ' ' . (synav()->is($itemKey) ? 'menu-item-icon-active' : 'menu-item-icon-inactive') }}"
            ></span>
        @endif

        @isset($item['sub'])
            <x-synapps::layouts.nav.arrow :itemKey="$itemKey" />
        @endisset

        <span
            class="menu-item-text"
            :class="sidebarToggle ? 'lg:hidden' : ''"
        >
            {{ synav()->translate($item['name']) }}
        </span>
    </a>

    @isset($item['sub'])
        <x-synapps::layouts.nav.submenu
            :items="$item['sub']"
            :parentKey="$itemKey"
        />
    @endisset
</li>
@endcanAccess
