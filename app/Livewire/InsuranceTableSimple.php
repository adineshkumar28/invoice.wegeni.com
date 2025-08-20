<?php

namespace App\Livewire;

use App\Models\Insurance;
use App\Models\Category;
use App\Models\Client;
use App\Models\User;
use App\Models\TenantWiseClient;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Stancl\Tenancy\Database\TenantScope;

class InsuranceTableSimple extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $categoryFilter = '';
    public $clientFilter = '';
    public $policyNumberFilter = '';
    public $startDateFrom = '';
    public $startDateTo = '';
    public $endDateFrom = '';
    public $endDateTo = '';
    public $premiumAmountFrom = '';
    public $premiumAmountTo = '';
    public $showAdvancedFilters = false;
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'clientFilter' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingClientFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
        $this->resetPage();
    }

    public function deleteInsurance($insuranceId)
    {
        try {
            $insurance = Insurance::where('id', $insuranceId)
                                 ->where('tenant_id', Auth::user()->tenant_id)
                                 ->first();

            if ($insurance) {
                $insuranceName = $insurance->name;
                $insurance->delete();
                
                session()->flash('success', "Insurance '{$insuranceName}' deleted successfully!");
                $this->dispatch('insurance-deleted');
                
                if ($this->insurances->count() === 0 && $this->insurances->currentPage() > 1) {
                    $this->resetPage();
                }
            } else {
                session()->flash('error', 'Insurance not found or access denied!');
            }
        } catch (\Exception $e) {
            \Log::error('Error deleting insurance: ' . $e->getMessage());
            session()->flash('error', 'Error deleting insurance: ' . $e->getMessage());
        }
    }

public function getInsurancesProperty()
{
    $query = Insurance::with(['client.user', 'category'])
                     ->where('tenant_id', Auth::user()->tenant_id);

    // Apply search
    if ($this->search) {
        $query->where(function ($q) {
            $q->where('name', 'like', '%' . $this->search . '%')
              ->orWhere('policy_number', 'like', '%' . $this->search . '%')
              ->orWhereHas('client.user', function ($userQuery) {
                  $userQuery->where(function ($q) {
                      $q->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                  });
              })
              ->orWhereHas('category', function ($categoryQuery) {
                  $categoryQuery->where('name', 'like', '%' . $this->search . '%');
              });
        });
    }

    // Apply filters
    if ($this->policyNumberFilter) {
        $query->where('policy_number', 'like', '%' . $this->policyNumberFilter . '%');
    }

    if ($this->startDateFrom) {
        $query->where('start_date', '>=', $this->startDateFrom);
    }

    if ($this->startDateTo) {
        $query->where('start_date', '<=', $this->startDateTo);
    }

    if ($this->endDateFrom) {
        $query->where('end_date', '>=', $this->endDateFrom);
    }

    if ($this->endDateTo) {
        $query->where('end_date', '<=', $this->endDateTo);
    }

    if ($this->premiumAmountFrom) {
        $query->where('premium_amount', '>=', $this->premiumAmountFrom);
    }

    if ($this->premiumAmountTo) {
        $query->where('premium_amount', '<=', $this->premiumAmountTo);
    }

    // Apply status filter
    if ($this->statusFilter) {
        switch ($this->statusFilter) {
            case 'active':
                $query->where('end_date', '>', Carbon::now());
                break;
            case 'expiring':
                $query->expiringSoon(30);
                break;
            case 'expired':
                $query->expired();
                break;
        }
    }

    // Apply category and client filters
    if ($this->categoryFilter) {
        $query->where('category_id', $this->categoryFilter);
    }

    if ($this->clientFilter) {
        $query->where('client_id', $this->clientFilter);
    }

    // Apply sorting
    if ($this->sortField === 'client_name') {
        $query->join('clients', 'insurances.client_id', '=', 'clients.id')
              ->orderBy('clients.first_name', $this->sortDirection)
              ->select('insurances.*');
    } elseif ($this->sortField === 'category_name') {
        $query->join('categories', 'insurances.category_id', '=', 'categories.id')
              ->orderBy('categories.name', $this->sortDirection)
              ->select('insurances.*');
    } else {
        $query->orderBy($this->sortField, $this->sortDirection);
    }

    return $query->paginate($this->perPage);
}

    public function getCategoriesProperty()
    {
        return Category::where('tenant_id', Auth::user()->tenant_id)
                      ->orderBy('name')
                      ->get();
    }

    public function getClientsProperty()
    {
        // Get clients using the same pattern as ClientController
        $tenantID = getLogInUser()->tenant_id;
        
        return User::with(['client', 'clients'])
            ->whereHas('clients', function ($q) use ($tenantID) {
                $q->where('tenant_id', $tenantID);
            })
            ->withoutGlobalScope(new TenantScope())
            ->get()
            ->map(function ($user) use ($tenantID) {
                $clientTenant = $user->clients()->where('tenant_id', $tenantID)->first();
                $clientData = $user->client;
                
                if ($clientData && $clientTenant) {
                    return (object) [
                        'id' => $clientData->id, // Use client->id, not clientTenant->client_id
                        'name' => trim($user->first_name . ' ' . $user->last_name), // Use user's name
                        'email' => $user->email
                    ];
                }
                return null;
            })
            ->filter()
            ->sortBy('name');
    }

    public function render()
    {
        return view('livewire.insurance-table-simple', [
            'insurances' => $this->insurances,
            'categories' => $this->categories,
            'clients' => $this->clients,
        ]);
    }
}
