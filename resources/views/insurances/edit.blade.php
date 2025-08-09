@extends('layouts.app')

@section('title')
    {{ __('Edit Insurance') }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-end mb-5">
                    <h1>@yield('title')</h1>
                    <div class="d-flex gap-2">
                        <a class="btn btn-outline-secondary" 
                           href="{{ route('insurances.show', $insurance->id) }}">
                            <i class="fas fa-eye"></i> {{ __('View') }}
                        </a>
                        <a class="btn btn-outline-primary" 
                           href="{{ route('insurances.index') }}">
                            <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                        </a>
                    </div>
                </div>
                
                <div class="col-12">
                    @include('layouts.errors')
                </div>

                <!-- Insurance Info Card -->
                <div class="card mb-5">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="mb-0">
                                <i class="fas fa-shield-alt text-primary me-2"></i>
                                {{ $insurance->name }}
                            </h3>
                            <p class="text-muted mb-0">Policy Number: {{ $insurance->policy_number }}</p>
                        </div>
                        <div class="card-toolbar">
                            @if($insurance->is_expired)
                                <span class="badge badge-light-danger fs-6">Expired</span>
                            @elseif($insurance->days_until_expiry <= 30)
                                <span class="badge badge-light-warning fs-6">
                                    Expires in {{ $insurance->days_until_expiry }} days
                                </span>
                            @else
                                <span class="badge badge-light-success fs-6">Active</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Edit Form -->
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['insurances.update', $insurance->id], 'method' => 'put', 'id' => 'insuranceEditForm']) }}
                        @include('insurances.edit_fields')
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load client details on page load if client is selected
            const clientSelect = document.getElementById('clientSelect');
            if (clientSelect && clientSelect.value) {
                loadClientDetails(clientSelect.value);
            }
        });
    </script>
    @endpush
@endsection
