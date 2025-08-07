<?php

namespace App\Livewire;

use Livewire\Component;

class SuperAdminDashboard extends Component
{
    public $totalUsers;
    public $totalRevenues;
    public $totalUserPlans;
    public $totalEnquiries;

    public function mount($data)
    {
        $this->totalUsers = $data['users'];
        $this->totalRevenues = $data['revenue'];
        $this->totalUserPlans = $data['activeUserPlan'];
        $this->totalEnquiries = $data['totalEnquiries'];
    }

    public function placeholder()
    {
        return view('livewire.super_admin_skeleton');
    }
    public function render()
    {
        return view('livewire.super-admin-dashboard');
    }
}
