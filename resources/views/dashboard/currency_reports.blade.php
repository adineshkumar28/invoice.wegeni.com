@extends('layouts.app')
@section('title')
    {{ __('messages.currency_reports') }}
@endsection
@section('content')
    <div class="container-fluid">
        <livewire:currency-report Lazy :$currencyData />
    </div>
@endsection
