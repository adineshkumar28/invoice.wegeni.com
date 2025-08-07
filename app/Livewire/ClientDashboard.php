<?php

namespace App\Livewire;

use Livewire\Component;

class ClientDashboard extends Component
{
    public $totalInvoices;
    public $totalPaidInvoices;
    public $totalUnpaidInvoices;

    public function mount($data)
    {
        $this->totalInvoices = $data['totalInvoices'];
        $this->totalPaidInvoices = $data['totalPaidInvoices'];
        $this->totalUnpaidInvoices = $data['totalUnpaidInvoices'];
    }

    public function placeholder()
    {
        return view('livewire.client_dashboard_skeleton');
    }

    public function render()
    {
        return view('livewire.client-dashboard');
    }
}
