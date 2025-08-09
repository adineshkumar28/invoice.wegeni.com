@extends('layouts.app')

@section('title')
    {{ __('Insurances') }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column">
            @include('flash::message')
            
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h1>{{ __('Insurances') }}</h1>
                <a class="btn btn-primary" href="{{ route('insurances.create') }}">
                    <i class="fas fa-plus"></i> {{ __('Add Insurance') }}
                </a>
            </div>

            <!-- Data Table -->
            <div class="card">
                <div class="card-body">
                    <livewire:simple-insurance-table />
                </div>
            </div>
        </div>
    </div>
@endsection
