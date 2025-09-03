@extends('layouts.app')

@section('title')
    {{ __('Edit Invoice') }}
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
                        <h1>{{ __('Edit Invoice') }} #{{ $invoice->invoice_id }}</h1>
                        <div>
                            <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-eye"></i> {{ __('View') }}
                            </a>
                            <a href="{{ route('invoices.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                {{ Form::model($invoice, ['route' => ['invoices.update', $invoice->id], 'method' => 'put', 'id' => 'invoiceEditForm', 'data-invoice-id' => $invoice->id]) }}
                @include('invoices.edit_fields')
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <!-- Hidden data for JavaScript -->
    <input type="hidden" id="insurances" value="{{ json_encode($associateInsurances) }}">
    <input type="hidden" id="taxes" value="{{ json_encode($associateTaxes) }}">
    <input type="hidden" id="currency" value="{{ getCurrencySymbol() }}">
    <div id="invoiceEditConfig"
         data-clients-by-group-url="{{ route('invoices.get-clients-by-group', ['groupId' => '__ID__']) }}"
         data-client-context-url="{{ route('invoices.get-client-context', ['userId' => '__ID__']) }}"
         data-insurance-url="{{ route('invoices.get-insurance', ['insuranceId' => '__ID__']) }}">
    </div>

<script src="{{ asset('assets/js/edit-invoice.js') }}"></script>
@endsection
