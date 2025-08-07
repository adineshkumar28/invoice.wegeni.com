<?php

namespace App\Livewire;

use Livewire\Component;

class AdminDashboard extends Component
{
    public $totalClients;
    public $totalInvoices;
    public $totalProducts;
    public $paidInvoices;
    public $unpaidInvoices;

    public function mount($dashboardData)
    {
        $this->totalClients = $dashboardData['totalClients'];
        $this->totalInvoices = $dashboardData['totalInvoices'];
        $this->totalProducts = $dashboardData['totalProducts'];
        $this->paidInvoices = $dashboardData['paidInvoices'];
        $this->unpaidInvoices = $dashboardData['unpaidInvoices'];
    }

    public function placeholder()
    {
        return view('livewire.admin_dashboard_skeleton');
    }

    public function render()
    {
        return view('livewire.admin-dashboard');
    }
}
