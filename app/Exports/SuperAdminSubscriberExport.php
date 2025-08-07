<?php

namespace App\Exports;

use App\Models\Subscriber;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SuperAdminSubscriberExport implements FromView
{
    public function view(): View
    {
        $subscribers = Subscriber::orderBy('created_at','desc')->get();

        return view('excel.super_admin_subscribers_excel', compact('subscribers'));
    }
}
