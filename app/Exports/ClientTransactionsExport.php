<?php

namespace App\Exports;

use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;

class ClientTransactionsExport implements FromView
{
    public function view(): View
    {
        $transactions = Payment::with('invoice.client.user')->where('user_id', Auth::id())->orderBy('created_at','desc')->get();

        return view('excel.client_transactions_excel', compact('transactions'));
    }
}
