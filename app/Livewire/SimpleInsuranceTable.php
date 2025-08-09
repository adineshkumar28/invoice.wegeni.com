<?php

namespace App\Livewire;

use App\Models\Insurance;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SimpleInsuranceTable extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $categoryFilter = '';
    public $clientFilter = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

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

    public function deleteInsurance($insuranceId)
    {
        try {
            $insurance = Insurance::where('id', $insuranceId)
                                 ->where('tenant_id', Auth::user()->tenant_id)
                                 ->first();

            if ($insurance) {
                $insurance->delete();
                session()->flash('success', 'Insurance deleted successfully!');
            } else {
                session()->flash('error', 'Insurance not found or access denied!');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting insurance: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = Insurance::with(['client', 'category'])
                         ->where('tenant_id', Auth::user()->tenant_id);

        // Apply search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('policy_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('client', function($clientQuery) {
                      $clientQuery->where('first_name', 'like', '%' . $this->search . '%')
                                  ->orWhere('last_name', 'like', '%' . $this->search . '%');
                  });
            });
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

        // Apply category filter
        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }

        // Apply client filter
        if ($this->clientFilter) {
            $query->where('client_id', $this->clientFilter);
        }

        $insurances = $query->orderBy('created_at', 'desc')
                           ->paginate($this->perPage);

        return view('livewire.simple-insurance-table', compact('insurances'));
    }
}
