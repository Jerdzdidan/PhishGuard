<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <p class="text-center pt-4 mb-0">(ADMIN)</p>
    <div class="app-brand demo mt-0 pt-0">
    {{-- <a href="{{ route('admin.home') }}" class="app-brand-link">
        <span class="app-brand-logo demo">
            <img src="{{ asset('img/landing/logo.png') }}" alt="">
        </span>
        <span class="app-brand-text demo menu-text fw-bold ms-4">PhishGuard</span>
    </a> --}}
   
        <a href="{{ route('admin.home') }}" class="app-brand-link">
        <span class="mt-0">
            <img src="{{ asset('img/landing/logo.png') }}" alt="" class="mt-0" style="width: 40px; height: auto; max-width: 100%;">
        </span>
        <span class="app-brand-text demo text-dark menu-text fw-bold ms-1 ps-1 mt-0">CyberWais</span>
        </a>
    </div>

    <div class="menu-divider mt-0"></div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        <!-- Home -->
        {{-- <li class="menu-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <a href="{{ route('home') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home"></i>
                <div class="text-truncate" data-i18n="Home">Home</div>
            </a>
        </li> --}}

        <x-sidebar.item route='admin.home' name='Home' icon='menu-icon tf-icons bx bx-home'/>

        @yield('menu_items')
    
    </ul>
</aside>