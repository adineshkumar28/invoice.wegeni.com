<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #333;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 15px;
        }
        
        .header {
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        
        .header-content {
            display: table;
            width: 100%;
        }
        
        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        
        .header-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: right;
        }
        
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 8px;
        }
        
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .invoice-number {
            font-size: 14px;
            color: #666;
        }
        
        .details-section {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        
        .bill-to-section {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }
        
        .invoice-info-section {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .section-header {
            font-size: 12px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 8px;
            text-transform: uppercase;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
        }
        
        .info-row {
            margin-bottom: 6px;
        }
        
        .info-label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            width: 80px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }
        
        .items-table th {
            background-color: #007bff;
            color: white;
            padding: 10px 6px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
        }
        
        .items-table td {
            padding: 8px 6px;
            border-bottom: 1px solid #eee;
            font-size: 10px;
        }
        
        .items-table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .summary-section {
            display: table;
            width: 100%;
        }
        
        .summary-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        
        .summary-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
        }
        
        .summary-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #eee;
        }
        
        .summary-table .total-row {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        
        .notes-box {
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #f8f9fa;
            margin-top: 15px;
        }
        
        .notes-title {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 8px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 9px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="header-left">
                    <div class="company-name">{{ config('app.name') }}</div>
                    <div>{{ getSettingValue('company_address') ?? 'Company Address' }}</div>
                    <div>{{ getSettingValue('company_phone') ?? 'Phone: +1 (555) 123-4567' }}</div>
                    <div>{{ getSettingValue('company_email') ?? 'Email: info@company.com' }}</div>
                </div>
                <div class="header-right">
                    <div class="invoice-title">INVOICE</div>
                    <div class="invoice-number">#{{ $invoice->invoice_id }}</div>
                </div>
            </div>
        </div>

        <!-- Details Section -->
        <div class="details-section">
            <div class="bill-to-section">
                <div class="section-header">Bill To</div>
                @if($invoice->client && $invoice->client->user)
                    <div><strong>{{ $invoice->client->user->first_name }} {{ $invoice->client->user->last_name }}</strong></div>
                    <div>{{ $invoice->client->user->email }}</div>
                    @if($invoice->client->user->contact)
                        <div>{{ $invoice->client->user->contact }}</div>
                    @endif
                    @if($invoice->client->address)
                        <div>{{ $invoice->client->address }}</div>
                    @endif
                    @if($invoice->client->clientGroup)
                        @php
                            $__group = $invoice->client->clientGroup;
                            $__count = $__group->clients ? $__group->clients->count() : $__group->clients()->count();
                        @endphp
                        <div>Group: <strong>{{ $__group->name }}</strong> ({{ $__count }})</div>
                    @endif
                @else
                    <div>Client information not available</div>
                @endif
            </div>
            <div class="invoice-info-section">
                <div class="section-header">Invoice Information</div>
                <div class="info-row">
                    <span class="info-label">Date:</span>
                    {{ $invoice->invoice_date->format('M d, Y') }}
                </div>
                <div class="info-row">
                    <span class="info-label">Due Date:</span>
                    {{ $invoice->due_date->format('M d, Y') }}
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    @php
                        $statusText = match($invoice->status) {
                            \App\Models\Invoice::PAID => 'Paid',
                            \App\Models\Invoice::UNPAID => 'Unpaid',
                            \App\Models\Invoice::DRAFT => 'Draft',
                            \App\Models\Invoice::OVERDUE => 'Overdue',
                            default => 'Unpaid'
                        };
                    @endphp
                    {{ $statusText }}
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 30%">Description</th>
                    <th style="width: 25%">Policy/Details</th>
                    <th style="width: 10%" class="text-center">Qty</th>
                    <th style="width: 15%" class="text-right">Price</th>
                    <th style="width: 15%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->invoiceItems as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            @if($item->insurance)
                                {{ $item->insurance->name }}
                            @elseif($item->insurance_name)
                                {{ $item->insurance_name }}
                            @elseif($item->product)
                                {{ $item->product->name }}
                            @elseif($item->product_name)
                                {{ $item->product_name }}
                            @else
                                Item
                            @endif
                        </td>
                        <td>
                            @if($item->policy_number)
                                Policy: {{ $item->policy_number }}<br>
                            @endif
                            @if($item->policy_start_date && $item->policy_end_date)
                                {{ $item->policy_start_date->format('M d, Y') }} - {{ $item->policy_end_date->format('M d, Y') }}
                            @endif
                        </td>
                        <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                        <td class="text-right">{{ getCurrencyAmount($item->price, true) }}</td>
                        <td class="text-right">{{ getCurrencyAmount($item->total, true) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-left">
                @if($invoice->note)
                    <div class="notes-box">
                        <div class="notes-title">Notes:</div>
                        <div>{{ $invoice->note }}</div>
                    </div>
                @endif
            </div>
            <div class="summary-right">
                <table class="summary-table">
                    <tr>
                        <td>Subtotal:</td>
                        <td class="text-right">{{ getCurrencyAmount($invoice->amount, true) }}</td>
                    </tr>
                    @if($invoice->discount > 0)
                        <tr>
                            <td>Discount:</td>
                            <td class="text-right">
                                @php
                                    $discountAmount = $invoice->discount_type == 2 
                                        ? ($invoice->amount * $invoice->discount / 100) 
                                        : $invoice->discount;
                                @endphp
                                -{{ getCurrencyAmount($discountAmount, true) }}
                            </td>
                        </tr>
                    @endif
                    @if(isset($totalTax) && array_sum($totalTax) > 0)
                        <tr>
                            <td>Tax:</td>
                            <td class="text-right">{{ getCurrencyAmount(array_sum($totalTax), true) }}</td>
                        </tr>
                    @endif
                    <tr class="total-row">
                        <td><strong>TOTAL:</strong></td>
                        <td class="text-right"><strong>{{ getCurrencyAmount($invoice->final_amount, true) }}</strong></td>
                    </tr>
                    @if(isset($paid))
                        <tr>
                            <td>Paid:</td>
                            <td class="text-right">{{ getCurrencyAmount($paid, true) }}</td>
                        </tr>
                    @endif
                    @if(isset($dueAmount))
                        <tr>
                            <td>Due:</td>
                            <td class="text-right">{{ getCurrencyAmount($dueAmount, true) }}</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>

        @if($invoice->term)
            <div class="notes-box">
                <div class="notes-title">Terms & Conditions:</div>
                <div>{{ $invoice->term }}</div>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your business!</p>
        </div>
    </div>
</body>
</html>
