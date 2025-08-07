@extends('layouts.app')
@section('title')
    {{ __('messages.dashboard') }}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column">
            <div class="row">
                <livewire:super-admin-dashboard lazy :$data />
                <div class="col-12 mb-4">
                    <div class="">
                        <div class="card mt-3">
                            <div class="card-body p-5">
                                <div class="card-header border-0 pt-5">
                                    <h3 class="mb-0">{{ __('messages.admin_dashboard.revenue_overview') }}</h3>
                                    <div class="ms-auto">
                                        <div id="rightData" class="date-picker-space">
                                            <input class="form-control removeFocus" id="super_admin_time_range">
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-lg-6 p-0">
                                    <div class="">
                                        <div id="revenue_overview-container" class="pt-2">
                                            <canvas id="revenue_chart_canvas" height="200" width="905"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    {{ Form::hidden('currency', getSuperAdminDefaultCurrency(), ['id' => 'currency']) }}
    {{ Form::hidden('currency_position', superAdminCurrencyPosition(), ['id' => 'currency_position']) }}
@endsection
