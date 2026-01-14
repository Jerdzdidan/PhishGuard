<li class="menu-item {{ $class }} {{ request()->routeIs($route) ? 'active' : '' }}">
    <a href="{{ route($route, $param) }}" class="menu-link">
        <i class="{{ $icon }}"></i>
        <div class="text-truncate" data-i18n="{{ Str::title($name) }}">
            {{ $name }}
        </div>
    </a>
</li>
