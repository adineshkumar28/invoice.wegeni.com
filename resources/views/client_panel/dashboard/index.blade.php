@extends('client_panel.layouts.app')
@section('title')
    {{ __('messages.dashboard') }}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column">
            <livewire:client-dashboard lazy :$data />
        </div>
    </div>
@endsection
