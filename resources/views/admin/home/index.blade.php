@extends('admin.layout.base')

@section('nav_title')
HOME
@endsection

@section('body')
<div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <img src="{{ asset('img/home/home.png') }}" alt="Home" class="img-fluid" style="max-width: 100%; height: auto; border-radius: 10px;">
</div>
@endsection