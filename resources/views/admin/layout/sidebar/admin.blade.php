@extends('admin.layout.sidebar.bar')

@section('menu_items')

<x-sidebar.item route='#' name='Dashboard' icon='menu-icon tf-icons bx bxs-dashboard'/>

<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Core</span>
</li>

<x-sidebar.item route='admin.lessons.index' name='Lessons' icon='menu-icon tf-icons bx bxs-book'/>

<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Management</span>
</li>

<x-sidebar.item route='admin.users.index' name='Users' icon='menu-icon tf-icons bx bxs-user' />

@endsection