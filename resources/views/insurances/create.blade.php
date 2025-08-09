@extends('layouts.app')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title')
    {{ __('Add Insurance') }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-end mb-5">
                    <h1>@yield('title')</h1>
                    <a class="btn btn-outline-primary float-end"
                       href="{{ route('insurances.index') }}">{{ __('Back') }}</a>
                </div>
                <div class="col-12">
                    @include('layouts.errors')
                </div>
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => 'insurances.store']) }}
                        @include('insurances.fields')
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
