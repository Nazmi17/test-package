<aside
    class="sidebar fixed left-0 top-0 z-20 flex h-screen w-[290px] flex-col overflow-y-hidden border-r border-gray-200 bg-white px-5 transition-all ease-linear lg:static lg:translate-x-0 dark:border-gray-800 dark:bg-black"
    :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full'"
>
    <div
        class="sidebar-header"
        :class="sidebarToggle ? 'justify-center' : 'justify-between'"
    >
        <a href="{{ backend_route('home.dashboard') }}">
            <span
                class="logo"
                :class="sidebarToggle ? 'hidden' : ''"
            >
                <img
                    class="dark:hidden"
                    src="{{ asset('assets/images/logo/logo.svg') }}"
                    alt="Logo"
                />
                <img
                    class="hidden dark:block"
                    src="{{ asset('assets/images/logo/logo-dark.svg') }}"
                    alt="Logo Dark"
                    x-cloak
                />
            </span>
            <img
                class="logo-icon"
                src="{{ asset('assets/images/logo/logo-icon.svg') }}"
                alt="Logo"
                :class="sidebarToggle ? 'lg:block' : 'hidden'"
            />
        </a>
    </div>
    <div class="menu-container">
        <nav x-data="{ selected: '{{ synav()->getActiveMenu() }}' }">
            <div>
                <h3 class="menu-heading">
                    <span
                        class="menu-group-title"
                        :class="sidebarToggle ? 'lg:hidden' : ''"
                    >
                        MENU
                    </span>
                    <svg
                        class="menu-group-icon mx-auto fill-current"
                        :class="sidebarToggle ? 'lg:block hidden' : 'hidden'"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        x-cloak
                    >
                        <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M5.99915 10.2451C6.96564 10.2451 7.74915 11.0286 7.74915 11.9951V12.0051C7.74915 12.9716 6.96564 13.7551 5.99915 13.7551C5.03265 13.7551 4.24915 12.9716 4.24915 12.0051V11.9951C4.24915 11.0286 5.03265 10.2451 5.99915 10.2451ZM17.9991 10.2451C18.9656 10.2451 19.7491 11.0286 19.7491 11.9951V12.0051C19.7491 12.9716 18.9656 13.7551 17.9991 13.7551C17.0326 13.7551 16.2491 12.9716 16.2491 12.0051V11.9951C16.2491 11.0286 17.0326 10.2451 17.9991 10.2451ZM13.7491 11.9951C13.7491 11.0286 12.9656 10.2451 11.9991 10.2451C11.0326 10.2451 10.2491 11.0286 10.2491 11.9951V12.0051C10.2491 12.9716 11.0326 13.7551 11.9991 13.7551C12.9656 13.7551 13.7491 12.9716 13.7491 12.0051V11.9951Z"
                            fill=""
                        />
                    </svg>
                </h3>
                <ul class="menu">
                    @foreach (synav()->backend() as $key => $menu)
                        <x-synapps::layouts.nav.menu-item
                            :item="$menu"
                            :itemKey="$key"
                        />
                    @endforeach
                </ul>
            </div>
        </nav>
    </div>
</aside>
