@extends('layouts.app')

@section('title')
    {{ __('Add Invoice') }}
@endsection

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1>{{ __('Add Invoice') }}</h1>
                        <a href="{{ route('invoices.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                {{ Form::open(['route' => 'invoices.store', 'id' => 'invoiceForm', 'name' => 'invoiceForm']) }}
                @include('invoices.fields')
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <!-- Hidden data for JavaScript -->
    <input type="hidden" id="insurances" value="{{ json_encode($associateInsurances) }}">
    <input type="hidden" id="taxes" value="{{ json_encode($associateTaxes) }}">
    <input type="hidden" id="currency" value="{{ getCurrencySymbol() }}">
    <script src="{{ asset('assets/js/create-invoice.js') }}"></script>

@endsection

