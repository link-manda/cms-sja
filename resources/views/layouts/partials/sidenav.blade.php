<!-- Start Sidebar -->
<aside class="app-menu" id="app-menu">
    <!-- Sidenav Menu Brand Logo -->
    <a class="logo-box sticky top-0 flex min-h-topbar-height items-center justify-start px-6 backdrop-blur-xs"
        href="{{ route('dashboard') }}">
        <!-- Light Brand Logo -->
        <div class="logo-light">
            <img alt="Light logo" class="logo-lg h-6" src="/images/logo-light.png" />
            <img alt="Small logo" class="logo-sm h-6" src="/images/logo-sm.png" />
        </div>
        <!-- Dark Brand Logo -->
        <div class="logo-dark">
            <img alt="Dark logo" class="logo-lg h-6" src="/images/logo-dark.png" />
            <img alt="Small logo" class="logo-sm h-6" src="/images/logo-sm.png" />
        </div>
    </a>
    <!-- Sidenav Menu Toggle Button -->
    <div class="absolute top-0 end-5 flex h-topbar items-center justify">
        <button class="" id="button-hover-toggle">
            <i class="iconify tabler--circle size-5"></i>
        </button>
    </div>
    <!-- Sidenav Menu Item Link -->
    <div class="relative min-h-0 flex-grow">
        <div class="size-full" data-simplebar="">
            <ul class="side-nav p-3 hs-accordion-group">
                <li class="menu-title">
                    <span>Overview</span>
                </li>
                <li class="menu-item">
                    <a class="menu-link" href="{{ route('dashboard') }}">
                        <span class="menu-icon"><i data-lucide="layout-dashboard"></i></span>
                        <div class="menu-text">Dashboard</div>
                    </a>
                </li>
                
                <li class="menu-title">
                    <span>Content Management</span>
                </li>
                <li class="menu-item">
                    <a class="menu-link" href="{{ route('projects.index') }}">
                        <span class="menu-icon"><i data-lucide="folder-kanban"></i></span>
                        <div class="menu-text">Projects</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a class="menu-link" href="{{ route('categories.index') }}">
                        <span class="menu-icon"><i data-lucide="tags"></i></span>
                        <div class="menu-text">Categories</div>
                    </a>
                </li>

                <li class="menu-title">
                    <span>System</span>
                </li>
                <li class="menu-item">
                    <a class="menu-link" href="{{ route('settings.index') }}">
                        <span class="menu-icon"><i data-lucide="settings"></i></span>
                        <div class="menu-text">Global Settings</div>
                    </a>
                </li>
                
                <li class="menu-title">
                    <span>Account</span>
                </li>
                <li class="menu-item">
                    <a class="menu-link" href="{{ route('profile.edit') }}">
                        <span class="menu-icon"><i data-lucide="user"></i></span>
                        <div class="menu-text">Profile</div>
                    </a>
                </li>
                <li class="menu-item">
                    <form method="POST" action="{{ route('logout') }}" id="logout-form" class="hidden">
                        @csrf
                    </form>
                    <a class="menu-link text-danger hover:text-danger/90 cursor-pointer" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <span class="menu-icon"><i data-lucide="log-out" class="text-danger"></i></span>
                        <div class="menu-text text-danger">Logout</div>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>
<!-- End Sidebar -->
