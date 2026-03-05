@props(['itemKey'])

<svg
    class="menu-item-arrow"
    :class="[
        selected.startsWith('{{ $itemKey }}') ? 'menu-item-arrow-active' : 'menu-item-arrow-inactive',
        sidebarToggle ? 'xl:hidden' : ''
    ]"
    width="20"
    height="20"
    viewBox="0 0 20 20"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
>
    <path
        d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585"
        stroke="currentColor"
        stroke-width="1.5"
        stroke-linecap="round"
        stroke-linejoin="round"
    ></path>
</svg>
