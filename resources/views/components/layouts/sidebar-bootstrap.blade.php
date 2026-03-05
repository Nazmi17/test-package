<!-- Sidebar -->
<ul
    class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion"
    id="accordionSidebar"
    :class="{ 'toggled': !sidebar_open, 'sidebar-toggled': !sidebar_open }"
    x-data="{ sidebar_open: true }"
>

    <!-- Sidebar - Brand -->
    <a
        class="sidebar-brand d-flex align-items-center justify-content-center"
        href="index.html"
    >
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">SB Admin <sup>2</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a
            class="nav-link"
            href="{{ route('home.dashboard') }}"
            wire:navigate
        >
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Home</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <li class="nav-item">
        <a
            class="nav-link"
            href="{{ route('example.index') }}"
            wire:navigate
        >
            <i class="fas fa-fw fa-database"></i>
            <span>Example</span></a>
    </li>

    <li class="nav-item">
        <a
            class="nav-link"
            href="{{ route('test.index') }}"
            wire:navigate
        >
            <i class="fas fa-fw fa-database"></i>
            <span>Test Module</span></a>
    </li>

    <li
        class="nav-item"
        x-data="{ opened: false }"
    >
        <a
            class="nav-link"
            data-toggle="collapse"
            data-target="#collapseTwo"
            href="#"
            aria-expanded="true"
            aria-controls="collapseC"
            @click="opened = !opened"
            :class="{ 'collapsed': !opened }"
        >
            <i class="fas fa-fw fa-cog"></i>
            <span>Components</span>
        </a>
        <div
            class="accordion-collapse collapse"
            id="collapseC"
            data-parent="#accordionSidebar"
            aria-labelledby="headingTwo"
            :class="{ 'show': opened }"
        >
            <div class="collapse-inner rounded bg-white py-2">
                <h6 class="collapse-header">Custom Components:</h6>
                <a
                    class="collapse-item"
                    href="buttons.html"
                >Buttons</a>
                <a
                    class="collapse-item"
                    href="cards.html"
                >Cards</a>
            </div>
        </div>
    </li>

    <!-- Heading -->
    <div class="sidebar-heading">
        Interface
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a
            class="nav-link collapsed"
            data-toggle="collapse"
            data-target="#collapseTwo"
            href="#"
            aria-expanded="true"
            aria-controls="collapseTwo"
        >
            <i class="fas fa-fw fa-cog"></i>
            <span>Components</span>
        </a>
        <div
            class="collapse"
            id="collapseTwo"
            data-parent="#accordionSidebar"
            aria-labelledby="headingTwo"
        >
            <div class="collapse-inner rounded bg-white py-2">
                <h6 class="collapse-header">Custom Components:</h6>
                <a
                    class="collapse-item"
                    href="buttons.html"
                >Buttons</a>
                <a
                    class="collapse-item"
                    href="cards.html"
                >Cards</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Utilities Collapse Menu -->
    <li class="nav-item">
        <a
            class="nav-link collapsed"
            data-toggle="collapse"
            data-target="#collapseUtilities"
            href="#"
            aria-expanded="true"
            aria-controls="collapseUtilities"
        >
            <i class="fas fa-fw fa-wrench"></i>
            <span>Utilities</span>
        </a>
        <div
            class="collapse"
            id="collapseUtilities"
            data-parent="#accordionSidebar"
            aria-labelledby="headingUtilities"
        >
            <div class="collapse-inner rounded bg-white py-2">
                <h6 class="collapse-header">Custom Utilities:</h6>
                <a
                    class="collapse-item"
                    href="utilities-color.html"
                >Colors</a>
                <a
                    class="collapse-item"
                    href="utilities-border.html"
                >Borders</a>
                <a
                    class="collapse-item"
                    href="utilities-animation.html"
                >Animations</a>
                <a
                    class="collapse-item"
                    href="utilities-other.html"
                >Other</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Addons
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a
            class="nav-link collapsed"
            data-toggle="collapse"
            data-target="#collapsePages"
            href="#"
            aria-expanded="true"
            aria-controls="collapsePages"
        >
            <i class="fas fa-fw fa-folder"></i>
            <span>Pages</span>
        </a>
        <div
            class="collapse"
            id="collapsePages"
            data-parent="#accordionSidebar"
            aria-labelledby="headingPages"
        >
            <div class="collapse-inner rounded bg-white py-2">
                <h6 class="collapse-header">Login Screens:</h6>
                <a
                    class="collapse-item"
                    href="login.html"
                >Login</a>
                <a
                    class="collapse-item"
                    href="register.html"
                >Register</a>
                <a
                    class="collapse-item"
                    href="forgot-password.html"
                >Forgot Password</a>
                <div class="collapse-divider"></div>
                <h6 class="collapse-header">Other Pages:</h6>
                <a
                    class="collapse-item"
                    href="404.html"
                >404 Page</a>
                <a
                    class="collapse-item"
                    href="blank.html"
                >Blank Page</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Charts -->
    <li class="nav-item">
        <a
            class="nav-link"
            href="charts.html"
        >
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Charts</span></a>
    </li>

    <!-- Nav Item - Tables -->
    <li class="nav-item">
        <a
            class="nav-link"
            href="tables.html"
        >
            <i class="fas fa-fw fa-table"></i>
            <span>Tables</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="d-none d-md-inline text-center">
        <button
            class="rounded-circle border-0"
            id="sidebarToggle"
            @click="sidebar_open = !sidebar_open"
        ></button>
    </div>

</ul>
<!-- End of Sidebar -->
