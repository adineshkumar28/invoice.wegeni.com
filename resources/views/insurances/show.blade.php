@extends('layouts.app')

@section('title')
    {{ __('Insurance Details') }}
@endsection

@section('header_toolbar')
    <div class="container-fluid">
        <div class="d-md-flex align-items-center justify-content-between mb-5">
            <h1 class="mb-0">@yield('title')</h1>
            <div class="text-end mt-4 mt-md-0">
                <a href="{{ route('insurances.edit', $insurance->id) }}"
                   class="btn btn-primary me-4">{{ __('Edit') }}</a>
                <a href="{{ route('insurances.index') }}"
                   class="btn btn-outline-primary">{{ __('Back') }}</a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        @include('flash::message')
        <div class="d-flex flex-column">
            @include('insurances.show_fields')
        </div>
    </div>
@endsection
