<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-xxl-5 col-12">
                <div class="d-sm-flex align-items-center mb-5 mb-xxl-0 text-center text-sm-start">
                    <div class="image image-circle image-small">
                        <i class="fas fa-shield-alt fa-3x text-primary"></i>
                    </div>
                    <div class="ms-0 ms-md-10 mt-5 mt-sm-0">
                        <h2>{{ $insurance->name }}</h2>
                        <p class="text-muted">{{ $insurance->policy_number }}</p>
                        
                        @if($insurance->is_expired)
                            <span class="badge badge-light-danger fs-6">Expired</span>
                        @elseif($insurance->days_until_expiry <= 30)
                            <span class="badge badge-light-warning fs-6">Expires in {{ $insurance->days_until_expiry }} days</span>
                        @else
                            <span class="badge badge-light-success fs-6">Active</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xxl-7 col-12">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card bg-light-primary">
                            <div class="card-body text-center">
                                <i class="fas fa-dollar-sign fa-2x text-primary mb-2"></i>
                                <h6 class="text-primary">Premium Amount</h6>
                                <h4 class="text-primary mb-0">{{ getCurrencyAmount($insurance->premium_amount, true) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light-info">
                            <div class="card-body text-center">
                                <i class="fas fa-calendar-alt fa-2x text-info mb-2"></i>
                                <h6 class="text-info">Policy Duration</h6>
                                <h4 class="text-info mb-0">{{ $insurance->start_date->diffInDays($insurance->end_date) }} Days</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-7 overflow-hidden">
    <ul class="nav nav-tabs mb-5 pb-1 overflow-auto flex-nowrap text-nowrap" id="myTab" role="tablist">
        <li class="nav-item position-relative me-7 mb-3" role="presentation">
            <button class="nav-link active p-0" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview"
                    type="button" role="tab" aria-controls="overview" aria-selected="true">
                {{ __('Overview') }}
            </button>
        </li>
        <li class="nav-item position-relative me-7 mb-3" role="presentation">
            <button class="nav-link p-0" id="client-tab" data-bs-toggle="tab" data-bs-target="#client"
                    type="button" role="tab" aria-controls="client" aria-selected="false">
                {{ __('Client Details') }}
            </button>
        </li>
        @if($insurance->custom_fields && count($insurance->custom_fields) > 0)
        <li class="nav-item position-relative me-7 mb-3" role="presentation">
            <button class="nav-link p-0" id="custom-tab" data-bs-toggle="tab" data-bs-target="#custom"
                    type="button" role="tab" aria-controls="custom" aria-selected="false">
                {{ __('Custom Fields') }}
            </button>
        </li>
        @endif
        <li class="nav-item position-relative me-7 mb-3" role="presentation">
            <button class="nav-link p-0" id="history-tab" data-bs-toggle="tab" data-bs-target="#history"
                    type="button" role="tab" aria-controls="history" aria-selected="false">
                {{ __('History') }}
            </button>
        </li>
    </ul>
    
    <div class="card">
        <div class="card-body">
            <div class="tab-content" id="myTabContent">
                <!-- Overview Tab -->
                <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                    <div class="row">
                        <div class="col-sm-6 d-flex flex-column mb-md-10 mb-5">
                            <label class="pb-2 fs-4 text-gray-600">{{ __('Insurance Name') }}:</label>
                            <span class="fs-4 text-gray-800 fw-bold">{{ $insurance->name }}</span>
                        </div>
                        <div class="col-sm-6 d-flex flex-column mb-md-10 mb-5">
                            <label class="pb-2 fs-4 text-gray-600">{{ __('Policy Number') }}:</label>
                            <span class="fs-4 text-gray-800 fw-bold">{{ $insurance->policy_number }}</span>
                        </div>
                        <div class="col-sm-6 d-flex flex-column mb-md-10 mb-5">
                            <label class="pb-2 fs-4 text-gray-600">{{ __('Premium Amount') }}:</label>
                            <span class="fs-4 text-gray-800 fw-bold text-success">{{ getCurrencyAmount($insurance->premium_amount, true) }}</span>
                        </div>
                        <div class="col-sm-6 d-flex flex-column mb-md-10 mb-5">
                            <label class="pb-2 fs-4 text-gray-600">{{ __('Category') }}:</label>
                            <span class="fs-4 text-gray-800">
                                @if($insurance->category)
                                    <span class="badge  text-gray-800  badge-light-primary fs-6">{{ $insurance->category->name }}</span>
                                @else
                                    {{ __('N/A') }}
                                @endif
                            </span>
                        </div>
                        <div class="col-sm-6 d-flex flex-column mb-md-10 mb-5">
                            <label class="pb-2 fs-4 text-gray-600">{{ __('Start Date') }}:</label>
                            <span class="fs-4 text-gray-800">{{ $insurance->start_date->format('d M Y') }}</span>
                        </div>
                        <div class="col-sm-6 d-flex flex-column mb-md-10 mb-5">
                            <label class="pb-2 fs-4 text-gray-600">{{ __('End Date') }}:</label>
                            <span class="fs-4 text-gray-800">{{ $insurance->end_date->format('d M Y') }}</span>
                        </div>
                        @if($insurance->description)
                        <div class="col-sm-12 d-flex flex-column mb-md-10 mb-5">
                            <label class="pb-2 fs-4 text-gray-600">{{ __('Description') }}:</label>
                            <span class="fs-4 text-gray-800">{{ $insurance->description }}</span>
                        </div>
                        @endif
                        
                        <!-- Policy Status Progress -->
                        <div class="col-sm-12 d-flex flex-column mb-md-10 mb-5">
                            <label class="pb-2 fs-4 text-gray-600">{{ __('Policy Status') }}:</label>
                            @php
                                $totalDays = $insurance->start_date->diffInDays($insurance->end_date);
                                $elapsedDays = now()->diffInDays($insurance->start_date);
                                $progress = $totalDays > 0 ? min(100, max(0, ($elapsedDays / $totalDays) * 100)) : 0;
                            @endphp
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-3" style="height: 20px;">
                                    <div class="progress-bar 
                                        @if($insurance->is_expired) bg-danger
                                        @elseif($insurance->days_until_expiry <= 30) bg-warning
                                        @else bg-success
                                        @endif" 
                                         role="progressbar" 
                                         style="width: {{ $progress }}%" 
                                         aria-valuenow="{{ $progress }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ number_format($progress, 1) }}%
                                    </div>
                                </div>
                                <span class="fs-6 text-muted">
                                    @if($insurance->is_expired)
                                        Expired
                                    @elseif($insurance->days_until_expiry <= 30)
                                        {{ $insurance->days_until_expiry }} days left
                                    @else
                                        {{ $insurance->days_until_expiry }} days remaining
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Client Details Tab -->
                <div class="tab-pane fade" id="client" role="tabpanel" aria-labelledby="client-tab">
                    @if($insurance->client)
                        @php
                            // Get client data properly
                            $client = $insurance->client;
                            $user = null;
                            
                            // Try to get user data from client relationship
                            if ($client && isset($client->user_id)) {
                                $user = \App\Models\User::withoutGlobalScope(\Stancl\Tenancy\Database\TenantScope::class)
                                                       ->find($client->user_id);
                            }
                            
                            // If no user found, try to get from client_name attribute
                            $clientName = $insurance->client_name ?? 'N/A';
                            $clientEmail = $user->email ?? $client->email ?? 'N/A';
                            $clientPhone = $user->contact ?? $client->phone ?? $client->contact ?? 'N/A';
                            $clientAddress = $client->address ?? 'N/A';
                            $clientCompany = $client->company_name ?? 'N/A';
                            $clientWebsite = $client->website ?? 'N/A';
                            $clientVat = $client->vat_no ?? 'N/A';
                            $clientPostal = $client->postal_code ?? 'N/A';
                        @endphp
                        
                        <div class="row">
                            <!-- Client Avatar and Basic Info -->
                            <div class="col-12 mb-5">
                                <div class="card bg-light-primary">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-circle symbol-50px overflow-hidden me-4">
                                                <div class="symbol-label fs-2 bg-primary text-white">
                                                    {{ strtoupper(substr($clientName, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="text-primary mb-1">{{ $clientName }}</h4>
                                                <p class="text-muted mb-0">{{ $clientEmail }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Contact Information -->
                            <div class="col-sm-6 d-flex flex-column mb-md-10 mb-5">
                                <label class="pb-2 fs-4 text-gray-600">
                                    <i class="fas fa-user text-primary me-2"></i>{{ __('Full Name') }}:
                                </label>
                                <span class="fs-4 text-gray-800">{{ $clientName }}</span>
                            </div>
                            
                            <div class="col-sm-6 d-flex flex-column mb-md-10 mb-5">
                                <label class="pb-2 fs-4 text-gray-600">
                                    <i class="fas fa-envelope text-primary me-2"></i>{{ __('Email') }}:
                                </label>
                                <span class="fs-4 text-gray-800">
                                    @if($clientEmail !== 'N/A')
                                        <a href="mailto:{{ $clientEmail }}" class="text-primary">{{ $clientEmail }}</a>
                                    @else
                                        {{ $clientEmail }}
                                    @endif
                                </span>
                            </div>
                            
                            <div class="col-sm-6 d-flex flex-column mb-md-10 mb-5">
                                <label class="pb-2 fs-4 text-gray-600">
                                    <i class="fas fa-phone text-primary me-2"></i>{{ __('Phone') }}:
                                </label>
                                <span class="fs-4 text-gray-800">
                                    @if($clientPhone !== 'N/A')
                                        <a href="tel:{{ $clientPhone }}" class="text-primary">{{ $clientPhone }}</a>
                                    @else
                                        {{ $clientPhone }}
                                    @endif
                                </span>
                            </div>
                            
                            <div class="col-sm-6 d-flex flex-column mb-md-10 mb-5">
                                <label class="pb-2 fs-4 text-gray-600">
                                    <i class="fas fa-building text-primary me-2"></i>{{ __('Company') }}:
                                </label>
                                <span class="fs-4 text-gray-800">{{ $clientCompany }}</span>
                            </div>
                            
                            @if($clientWebsite !== 'N/A')
                            <div class="col-sm-6 d-flex flex-column mb-md-10 mb-5">
                                <label class="pb-2 fs-4 text-gray-600">
                                    <i class="fas fa-globe text-primary me-2"></i>{{ __('Website') }}:
                                </label>
                                <span class="fs-4 text-gray-800">
                                    <a href="{{ $clientWebsite }}" target="_blank" class="text-primary">{{ $clientWebsite }}</a>
                                </span>
                            </div>
                            @endif
                            
                            @if($clientVat !== 'N/A')
                            <div class="col-sm-6 d-flex flex-column mb-md-10 mb-5">
                                <label class="pb-2 fs-4 text-gray-600">
                                    <i class="fas fa-hashtag text-primary me-2"></i>{{ __('VAT Number') }}:
                                </label>
                                <span class="fs-4 text-gray-800">{{ $clientVat }}</span>
                            </div>
                            @endif
                            
                            @if($clientAddress !== 'N/A')
                            <div class="col-sm-12 d-flex flex-column mb-md-10 mb-5">
                                <label class="pb-2 fs-4 text-gray-600">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>{{ __('Address') }}:
                                </label>
                                <span class="fs-4 text-gray-800">
                                    {{ $clientAddress }}
                                    @if($clientPostal !== 'N/A')
                                        <br><small class="text-muted">{{ __('Postal Code') }}: {{ $clientPostal }}</small>
                                    @endif
                                </span>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-10">
                            <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">{{ __('No Client Assigned') }}</h4>
                            <p class="text-muted">{{ __('This insurance policy does not have a client assigned.') }}</p>
                        </div>
                    @endif
                </div>

                <!-- Custom Fields Tab -->
                @if($insurance->custom_fields && count($insurance->custom_fields) > 0)
                <div class="tab-pane fade" id="custom" role="tabpanel" aria-labelledby="custom-tab">
                    <div class="row">
                        @foreach($insurance->custom_fields as $index => $field)
                        <div class="col-sm-6 d-flex flex-column mb-md-10 mb-5">
                            <label class="pb-2 fs-4 text-gray-600">
                                <i class="fas fa-tag text-primary me-2"></i>{{ $field['name'] }}:
                            </label>
                            <span class="fs-4 text-gray-800">{{ $field['value'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- History Tab -->
                <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light-success mb-5">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-plus fa-3x text-success me-4"></i>
                                        <div>
                                            <h5 class="text-success mb-1">{{ __('Created Date') }}</h5>
                                            <p class="mb-1 fw-bold">{{ $insurance->created_at->format('d M Y, h:i A') }}</p>
                                            <small class="text-muted">{{ $insurance->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card bg-light-warning mb-5">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-edit fa-3x text-warning me-4"></i>
                                        <div>
                                            <h5 class="text-warning mb-1">{{ __('Last Updated') }}</h5>
                                            <p class="mb-1 fw-bold">{{ $insurance->updated_at->format('d M Y, h:i A') }}</p>
                                            <small class="text-muted">{{ $insurance->updated_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Policy Timeline -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i class="fas fa-timeline text-primary me-2"></i>
                                        {{ __('Policy Timeline') }}
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="timeline">
                                        <div class="timeline-item">
                                            <div class="timeline-line w-40px"></div>
                                            <div class="timeline-icon symbol symbol-circle symbol-40px">
                                                <div class="symbol-label bg-success">
                                                    <i class="fas fa-play text-white fs-4"></i>
                                                </div>
                                            </div>
                                            <div class="timeline-content mb-10 mt-n1">
                                                <div class="pe-3 mb-5">
                                                    <div class="fs-5 fw-bold mb-2">{{ __('Policy Started') }}</div>
                                                    <div class="d-flex align-items-center mt-1 fs-6">
                                                        <div class="text-muted me-2 fs-7">{{ $insurance->start_date->format('d M Y') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if(!$insurance->is_expired)
                                        <div class="timeline-item">
                                            <div class="timeline-line w-40px"></div>
                                            <div class="timeline-icon symbol symbol-circle symbol-40px">
                                                <div class="symbol-label bg-primary">
                                                    <i class="fas fa-clock text-white fs-4"></i>
                                                </div>
                                            </div>
                                            <div class="timeline-content mb-10 mt-n1">
                                                <div class="pe-3 mb-5">
                                                    <div class="fs-5 fw-bold mb-2">{{ __('Current Status') }}</div>
                                                    <div class="d-flex align-items-center mt-1 fs-6">
                                                        <div class="text-muted me-2 fs-7">
                                                            @if($insurance->days_until_expiry <= 30)
                                                                {{ __('Expiring Soon') }} - {{ $insurance->days_until_expiry }} {{ __('days left') }}
                                                            @else
                                                                {{ __('Active') }} - {{ $insurance->days_until_expiry }} {{ __('days remaining') }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        <div class="timeline-item">
                                            <div class="timeline-line w-40px"></div>
                                            <div class="timeline-icon symbol symbol-circle symbol-40px">
                                                <div class="symbol-label {{ $insurance->is_expired ? 'bg-danger' : 'bg-warning' }}">
                                                    <i class="fas fa-stop text-white fs-4"></i>
                                                </div>
                                            </div>
                                            <div class="timeline-content mb-10 mt-n1">
                                                <div class="pe-3 mb-5">
                                                    <div class="fs-5 fw-bold mb-2">
                                                        {{ $insurance->is_expired ? __('Policy Expired') : __('Policy Expires') }}
                                                    </div>
                                                    <div class="d-flex align-items-center mt-1 fs-6">
                                                        <div class="text-muted me-2 fs-7">{{ $insurance->end_date->format('d M Y') }}</div>
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
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
}

.timeline-item {
    display: flex;
    position: relative;
}

.timeline-line {
    position: absolute;
    left: 20px;
    top: 40px;
    height: calc(100% - 40px);
    border-left: 1px dashed #e4e6ef;
}

.timeline-item:last-child .timeline-line {
    display: none;
}

.timeline-icon {
    position: relative;
    z-index: 1;
}

.timeline-content {
    flex: 1;
    margin-left: 20px;
}
</style>
