<div>
    <!-- Search and Filters -->
    <div class="row mb-4">
        <div class="col-md-3">
            <input type="text" wire:model.live="search" class="form-control" placeholder="Search insurances...">
        </div>
        <div class="col-md-2">
            <select wire:model.live="statusFilter" class="form-select">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="expiring">Expiring Soon</option>
                <option value="expired">Expired</option>
            </select>
        </div>
        <div class="col-md-2">
            <select wire:model.live="categoryFilter" class="form-select">
                <option value="">All Categories</option>
                @foreach(\App\Models\Category::where('tenant_id', Auth::user()->tenant_id)->get() as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select wire:model.live="clientFilter" class="form-select">
                <option value="">All Clients</option>
                @foreach(\App\Models\Client::where('tenant_id', Auth::user()->tenant_id)->get() as $client)
                    <option value="{{ $client->id }}">{{ $client->first_name }} {{ $client->last_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select wire:model.live="perPage" class="form-select">
                <option value="10">10 per page</option>
                <option value="25">25 per page</option>
                <option value="50">50 per page</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>Policy Number</th>
                    <th>Client</th>
                    <th>Category</th>
                    <th>Premium Amount</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($insurances as $insurance)
                    <tr class="
                        @if($insurance->is_expired) table-danger
                        @elseif($insurance->days_until_expiry <= 30) table-warning
                        @endif
                    ">
                        <td>{{ $insurance->name }}</td>
                        <td>{{ $insurance->policy_number }}</td>
                        <td>
                            @if($insurance->client)
                                {{ $insurance->client->first_name }} {{ $insurance->client->last_name }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @if($insurance->category)
                                {{ $insurance->category->name }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ getCurrencyAmount($insurance->premium_amount, true) }}</td>
                        <td>{{ $insurance->start_date->format('d M Y') }}</td>
                        <td>{{ $insurance->end_date->format('d M Y') }}</td>
                        <td>
                            @if($insurance->is_expired)
                                <span class="badge bg-danger">Expired</span>
                            @elseif($insurance->days_until_expiry <= 30)
                                <span class="badge bg-warning">Expiring Soon</span>
                            @else
                                <span class="badge bg-success">Active</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('insurances.show', $insurance->id) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('insurances.edit', $insurance->id) }}" 
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete({{ $insurance->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">No insurances found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
            Showing {{ $insurances->firstItem() ?? 0 }} to {{ $insurances->lastItem() ?? 0 }} of {{ $insurances->total() }} results
        </div>
        <div>
            {{ $insurances->links() }}
        </div>
    </div>

    <script>
        function confirmDelete(insuranceId) {
            if (confirm('Are you sure you want to delete this insurance?')) {
                @this.call('deleteInsurance', insuranceId);
            }
        }
    </script>
</div>
