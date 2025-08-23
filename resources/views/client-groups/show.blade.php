@extends('layouts.app')
@section('title')
    {{ $clientGroup->name }}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-end mb-5">
                    <h1>{{ $clientGroup->name }}</h1>
                    <div class="float-end">
                        <a href="{{ route('client-groups.edit', $clientGroup->id) }}" class="btn btn-primary me-2">
                            {{ __('messages.common.edit') }}
                        </a>
                        <a class="btn btn-outline-primary" href="{{ route('client-groups.index') }}">
                            {{ __('messages.common.back') }}
                        </a>
                    </div>
                </div>
                
                <!-- Group Details Card -->
                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('messages.client_group.group_details') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <strong>{{ __('messages.client_group.group_name') }}:</strong>
                                    <span class="ms-2">{{ $clientGroup->name }}</span>
                                </div>
                                <div class="mb-3">
                                    <strong>{{ __('messages.client_group.group_type') }}:</strong>
                                    <span class="ms-2 badge badge-light-primary">{{ ucfirst($clientGroup->group_type) }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <strong>{{ __('messages.client_group.total_clients') }}:</strong>
                                    <span class="ms-2 badge badge-light-success">{{ $clientGroup->clients_count }}</span>
                                </div>
                                <div class="mb-3">
                                    <strong>{{ __('messages.common.created_at') }}:</strong>
                                    <span class="ms-2">{{ $clientGroup->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                        @if($clientGroup->description)
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <strong>{{ __('messages.client_group.description') }}:</strong>
                                        <p class="mt-2">{{ $clientGroup->description }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Clients in Group -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('messages.client_group.clients_in_group') }}</h3>
                        <div class="card-toolbar">
                            <a href="{{ route('clients.create', ['group_id' => $clientGroup->id]) }}" class="btn btn-sm btn-primary">
                                {{ __('messages.client.add_client') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <livewire:client-group-clients-table clientGroupId="{{ $clientGroup->id }}"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
