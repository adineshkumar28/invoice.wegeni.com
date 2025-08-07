<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RemoveInvoiceWithoutClient extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $invoices = Invoice::get();

        foreach ($invoices as $invoice) {
            $client = Client::find($invoice->client_id);
            if (!$client) {
                $invoice->delete();
            }
        }
    }
}
