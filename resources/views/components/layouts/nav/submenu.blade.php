@props(['items', 'parentKey'])

<div
    class="translate block transform overflow-hidden"
    x-show="selected.startsWith('{{ $parentKey }}')"
    x-transition
>
    <ul
        class="menu-dropdown mt-2 flex flex-col gap-1 pl-9"
        :class="sidebarToggle ? 'xl:hidden' : 'flex'"
    >
        @foreach ($items as $key => $item)
            <x-synapps::layouts.nav.submenu-item
                :item="$item"
                :itemKey="$parentKey . '.' . $key"
            />
        @endforeach
    </ul>
</div>
