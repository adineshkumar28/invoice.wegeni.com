<div>
    <!-- Search and Filters -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       class="form-control" 
                       placeholder="Search insurances...">
                @if($search)
                    <button class="btn btn-outline-secondary" 
                            type="button" 
                            wire:click="$set('search', '')">
                        <i class="fas fa-times"></i>
                    </button>
                @endif
            </div>
        </div>
        
        <div class="col-md-2 mb-3">
            <select wire:model.live="statusFilter" class="form-select">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="expiring">Expiring Soon</option>
                <option value="expired">Expired</option>
            </select>
        </div>
        
        <div class="col-md-2 mb-3">
            <select wire:model.live="categoryFilter" class="form-select">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="col-md-2 mb-3"> <!-- Added client group filter select -->
            <select wire:model.live="clientGroupFilter" class="form-select">
                <option value="">All Client Groups</option>
                @foreach($clientGroups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="col-md-2 mb-3">
            <select wire:model.live="clientFilter" class="form-select">
                <option value="">All Clients</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="col-md-1 mb-3">
            <select wire:model.live="perPage" class="form-select">
                <option value="10">10 per page</option>
                <option value="25">25 per page</option>
                <option value="50">50 per page</option>
                <option value="100">100 per page</option>
            </select>
        </div>
    </div>

    <!-- Added advanced filters section -->
    <div class="row mb-3">
        <div class="col-12">
            <button type="button" 
                    class="btn btn-outline-secondary btn-sm me-2" 
                    wire:click="toggleAdvancedFilters">
                <i class="fas fa-filter"></i>
                {{ $showAdvancedFilters ? 'Hide' : 'Show' }} Advanced Filters
            </button>
            
            @if($search || $statusFilter || $categoryFilter || $clientFilter || $clientGroupFilter || $policyNumberFilter || $startDateFrom || $startDateTo || $endDateFrom || $endDateTo || $premiumAmountFrom || $premiumAmountTo) <!-- Added clientGroupFilter to condition -->
                <button type="button" 
                        class="btn btn-outline-danger btn-sm" 
                        wire:click="clearAllFilters">
                    <i class="fas fa-times"></i>
                    Clear All Filters
                </button>
            @endif
        </div>
    </div>

    @if($showAdvancedFilters)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Advanced Filters</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Policy Number Filter -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Policy Number</label>
                        <input type="text" 
                               wire:model.live.debounce.300ms="policyNumberFilter" 
                               class="form-control" 
                               placeholder="Enter policy number...">
                    </div>

                    <!-- Start Date Range -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Start Date From</label>
                        <input type="date" 
                               wire:model.live="startDateFrom" 
                               class="form-control">
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Start Date To</label>
                        <input type="date" 
                               wire:model.live="startDateTo" 
                               class="form-control">
                    </div>

                    <!-- End Date Range -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">End Date From</label>
                        <input type="date" 
                               wire:model.live="endDateFrom" 
                               class="form-control">
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">End Date To</label>
                        <input type="date" 
                               wire:model.live="endDateTo" 
                               class="form-control">
                    </div>

                    <!-- Premium Amount Range -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Premium From</label>
                        <input type="number" 
                               wire:model.live.debounce.300ms="premiumAmountFrom" 
                               class="form-control" 
                               placeholder="Min amount..."
                               step="0.01">
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Premium To</label>
                        <input type="number" 
                               wire:model.live.debounce.300ms="premiumAmountTo" 
                               class="form-control" 
                               placeholder="Max amount..."
                               step="0.01">
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Loading indicator -->
    <div wire:loading class="text-center py-3">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive" wire:loading.remove>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>
                        <button class="btn btn-link text-black p-0 text-decoration-none" 
                                wire:click="sortBy('name')">
                            Name
                            @if($sortField === 'name')
                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @else
                                <i class="fas fa-sort text-muted"></i>
                            @endif
                        </button>
                    </th>
                    <th>
                        <button class="btn btn-link text-black p-0 text-decoration-none" 
                                wire:click="sortBy('policy_number')">
                            Policy Number
                            @if($sortField === 'policy_number')
                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @else
                                <i class="fas fa-sort text-muted"></i>
                            @endif
                        </button>
                    </th>
                    <th>
                        <button class="btn btn-link text-black p-0 text-decoration-none" 
                                wire:click="sortBy('client_name')">
                            Client
                            @if($sortField === 'client_name')
                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @else
                                <i class="fas fa-sort text-muted"></i>
                            @endif
                        </button>
                    </th>
                    <th>
    <button class="btn btn-link text-black p-0 text-decoration-none" 
            wire:click="sortBy('client_group_name')">
        Client Group
        @if($sortField === 'client_group_name')
            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
        @else
            <i class="fas fa-sort text-muted"></i>
        @endif
    </button>
</th>

                    <th>
                        <button class="btn btn-link text-black p-0 text-decoration-none" 
                                wire:click="sortBy('category_name')">
                            Category
                            @if($sortField === 'category_name')
                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @else
                                <i class="fas fa-sort text-muted"></i>
                            @endif
                        </button>
                    </th>
                    <th>
                        <button class="btn btn-link text-black p-0 text-decoration-none" 
                                wire:click="sortBy('premium_amount')">
                            Premium Amount
                            @if($sortField === 'premium_amount')
                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @else
                                <i class="fas fa-sort text-muted"></i>
                            @endif
                        </button>
                    </th>
                    <th>
                        <button class="btn btn-link text-black p-0 text-decoration-none" 
                                wire:click="sortBy('start_date')">
                            Start Date
                            @if($sortField === 'start_date')
                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @else
                                <i class="fas fa-sort text-muted"></i>
                            @endif
                        </button>
                    </th>
                    <th>
                        <button class="btn btn-link text-black p-0 text-decoration-none" 
                                wire:click="sortBy('end_date')">
                            End Date
                            @if($sortField === 'end_date')
                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @else
                                <i class="fas fa-sort text-muted"></i>
                            @endif
                        </button>
                    </th>
                    <th style="color: black">Status</th>
                    <th style="color: black">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($insurances as $insurance)
                    <tr class="
                        @if($insurance->is_expired) table-danger
                        @elseif($insurance->days_until_expiry <= 30) table-warning
                        @endif
                    ">
                        <td>
                            <strong>{{ $insurance->name }}</strong>
                        </td>
                        <td>
                            <code>{{ $insurance->policy_number }}</code>
                        </td>
                        <td>
                            @if($insurance->client)
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-3">
                                        <div class="symbol-label">
                                            <div class="symbol-label fs-3 bg-light-primary text-primary">
                                                {{ strtoupper(substr($insurance->client->first_name, 0, 1)) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <strong>{{ $insurance->client_name }}</strong>
                                        @if($insurance->client->email)
                                            <small class="text-muted">{{ $insurance->client->email }}</small>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
    @if($insurance->client && $insurance->client->clientGroup)
        <span class="badge bg-info">{{ $insurance->client->clientGroup->name }}</span>
    @else
        <span class="text-muted">N/A</span>
    @endif
</td>

                        <td>
                            @if($insurance->category)
                                <span class="badge bg-secondary">{{ $insurance->category->name }}</span>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ getCurrencyAmount($insurance->premium_amount, true) }}</strong>
                        </td>
                        <td>{{ $insurance->start_date->format('d M Y') }}</td>
                        <td>{{ $insurance->end_date->format('d M Y') }}</td>
                        <td>
                            @if($insurance->is_expired)
                                <span class="badge bg-danger">Expired</span>
                            @elseif($insurance->days_until_expiry <= 30)
                                <span class="badge bg-warning text-dark">
                                    Expiring in {{ $insurance->days_until_expiry }} days
                                </span>
                            @else
                                <span class="badge bg-success">Active</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('insurances.show', $insurance->id) }}" 
                                   class="btn btn-sm btn-outline-primary"
                                   title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('insurances.edit', $insurance->id) }}" 
                                   class="btn btn-sm btn-outline-secondary"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger"
                                        title="Delete"
                                        onclick="confirmDelete({{ $insurance->id }}, '{{ $insurance->name }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-shield-alt fa-3x mb-3"></i>
                                <h5>No insurances found</h5>
                                <p>Try adjusting your search criteria or <a href="{{ route('insurances.create') }}">create a new insurance</a>.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($insurances->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Showing {{ $insurances->firstItem() ?? 0 }} to {{ $insurances->lastItem() ?? 0 }} 
                of {{ $insurances->total() }} results
            </div>
            <div>
                {{ $insurances->links() }}
            </div>
        </div>
    @endif

    <script>
        function confirmDelete(insuranceId, insuranceName) {
            // Check if SweetAlert is available
            if (typeof Swal === 'undefined') {
                if (confirm(`Are you sure you want to delete "${insuranceName}"? This action cannot be undone!`)) {
                    Livewire.find('{{ $this->getId() }}').call('deleteInsurance', insuranceId);
                }
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete "${insuranceName}". This action cannot be undone!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Deleting...',
                        text: 'Please wait while we delete the insurance.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Call Livewire method
                    Livewire.find('{{ $this->getId() }}').call('deleteInsurance', insuranceId);
                }
            });
        }

        // Listen for Livewire events - Updated for Livewire v3
        document.addEventListener('DOMContentLoaded', function() {
            // Listen for insurance-deleted event
            Livewire.on('insurance-deleted', () => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Insurance has been deleted successfully.',
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    alert('Insurance deleted successfully!');
                }
            });

            // Listen for flash messages
            @if(session('success'))
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Success!',
                        text: '{{ session('success') }}',
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            @endif

            @if(session('error'))
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Error!',
                        text: '{{ session('error') }}',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                } else {
                    alert('{{ session('error') }}');
                }
            @endif
        });
    </script>
</div>