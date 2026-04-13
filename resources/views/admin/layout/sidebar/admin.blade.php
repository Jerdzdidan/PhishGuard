@extends('admin.layout.sidebar.bar')

@section('menu_items')

<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Core</span>
</li>

<x-sidebar.item route='admin.lessons.index' name='Lessons' icon='menu-icon tf-icons bx bxs-book'/>

{{-- Sections - visible to both admin and teacher --}}
<x-sidebar.item route='admin.sections.index' name='Sections' icon='menu-icon tf-icons bx bxs-group'/>

{{-- Simulations --}}
{{-- <x-sidebar.item route='admin.scenarios.index' name='Simulations' icon='menu-icon tf-icons bx bxs-shield'/> --}}

<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Insights</span>
</li>

<!-- Analytics Accordion -->
<li class="menu-item {{ request()->routeIs('admin.analytics.*') ? 'active open' : '' }}">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
        <div data-i18n="Analytics">Analytics</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs('admin.analytics.overview') ? 'active' : '' }}">
            <a href="{{ route('admin.analytics.overview') }}" class="menu-link">
                <div data-i18n="Overview">Overview</div>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.analytics.quiz') ? 'active' : '' }}">
            <a href="{{ route('admin.analytics.quiz') }}" class="menu-link">
                <div data-i18n="Quiz Analytics">Quiz Analytics</div>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.analytics.simulation') ? 'active' : '' }}">
            <a href="{{ route('admin.analytics.simulation') }}" class="menu-link">
                <div data-i18n="Simulation Analytics">Simulation Analytics</div>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.analytics.assessment') ? 'active' : '' }}">
            <a href="{{ route('admin.analytics.assessment') }}" class="menu-link">
                <div data-i18n="Assessment Analytics">Assessment Analytics</div>
            </a>
        </li>
    </ul>
</li>

<!-- User Progress Accordion -->
<li class="menu-item {{ request()->routeIs('admin.user-progress.*') ? 'active open' : '' }}">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-user-check"></i>
        <div data-i18n="User Progress">User Progress</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs('admin.user-progress.index') ? 'active' : '' }}">
            <a href="{{ route('admin.user-progress.index') }}" class="menu-link">
                <div data-i18n="Progress Overview">Progress Overview</div>
            </a>
        </li>
    </ul>
</li>

@if(auth()->user()->isAdmin())
<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Management</span>
</li>

<x-sidebar.item route='admin.users.index' name='Users' icon='menu-icon tf-icons bx bxs-user' />
@endif

@endsection
