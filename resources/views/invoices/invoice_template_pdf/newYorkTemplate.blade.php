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
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        
        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .header-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }
        
        .company-info h1 {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .company-info p {
            margin-bottom: 5px;
            color: #666;
        }
        
        .invoice-info h2 {
            font-size: 28px;
            color: #e74c3c;
            margin-bottom: 10px;
        }
        
        .invoice-details {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        
        .bill-to {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .invoice-meta {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        
        .client-info p {
            margin-bottom: 5px;
        }
        
        .meta-row {
            margin-bottom: 8px;
        }
        
        .meta-label {
            font-weight: bold;
            color: #666;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .items-table th {
            background-color: #34495e;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
        }
        
        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #ddd;
        }
        
        .items-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .totals-section {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        
        .totals-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        
        .totals-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
        }
        
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .totals-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #ddd;
        }
        
        .totals-table .total-row {
            background-color: #34495e;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }
        
        .notes-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #34495e;
        }
        
        .notes-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .notes-content {
            color: #666;
            line-height: 1.6;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-paid {
            background-color: #27ae60;
            color: white;
        }
        
        .status-unpaid {
            background-color: #e74c3c;
            color: white;
        }
        
        .status-draft {
            background-color: #95a5a6;
            color: white;
        }
        
        .status-overdue {
            background-color: #f39c12;
            color: white;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header Section -->
        <div class="header">
            <div class="header-left">
                <div class="company-info">
                    <h1>{{ config('app.name') }}</h1>
                    <p>{{ getSettingValue('company_address') ?? 'Company Address' }}</p>
                    <p>{{ getSettingValue('company_phone') ?? 'Phone: +1 (555) 123-4567' }}</p>
                    <p>{{ getSettingValue('company_email') ?? 'Email: info@company.com' }}</p>
                </div>
            </div>
            <div class="header-right">
                <div class="invoice-info">
                    <h2>INVOICE</h2>
                    <p><strong>#{{ $invoice->invoice_id }}</strong></p>
                    @php
                        $statusClass = match($invoice->status) {
                            \App\Models\Invoice::PAID => 'status-paid',
                            \App\Models\Invoice::UNPAID => 'status-unpaid',
                            \App\Models\Invoice::DRAFT => 'status-draft',
                            \App\Models\Invoice::OVERDUE => 'status-overdue',
                            default => 'status-unpaid'
                        };
                        
                        $statusText = match($invoice->status) {
                            \App\Models\Invoice::PAID => 'Paid',
                            \App\Models\Invoice::UNPAID => 'Unpaid',
                            \App\Models\Invoice::DRAFT => 'Draft',
                            \App\Models\Invoice::OVERDUE => 'Overdue',
                            default => 'Unpaid'
                        };
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                </div>
            </div>
        </div>

        <!-- Invoice Details Section -->
        <div class="invoice-details">
            <div class="bill-to">
                <div class="section-title">Bill To:</div>
                <div class="client-info">
                    @if($invoice->client && $invoice->client->user)
                        <p><strong>{{ $invoice->client->user->first_name }} {{ $invoice->client->user->last_name }}</strong></p>
                        <p>{{ $invoice->client->user->email }}</p>
                        @if($invoice->client->user->contact)
                            <p>{{ $invoice->client->user->contact }}</p>
                        @endif
                        @if($invoice->client->address)
                            <p>{{ $invoice->client->address }}</p>
                        @endif
                        @if($invoice->client->company_name)
                            <p><em>{{ $invoice->client->company_name }}</em></p>
                        @endif
                        @if($invoice->client->clientGroup)
                            @php
                                $__group = $invoice->client->clientGroup;
                                $__count = $__group->clients ? $__group->clients->count() : $__group->clients()->count();
                            @endphp
                            <p><strong>Group:</strong> {{ $__group->name }} <span class="text-muted">({{ $__count }} members)</span></p>
                        @endif
                    @else
                        <p>Client information not available</p>
                    @endif
                </div>
            </div>
            <div class="invoice-meta">
                <div class="meta-row">
                    <span class="meta-label">Invoice Date:</span>
                    {{ $invoice->invoice_date->format('M d, Y') }}
                </div>
                <div class="meta-row">
                    <span class="meta-label">Due Date:</span>
                    {{ $invoice->due_date->format('M d, Y') }}
                </div>
                @if($invoice->currency)
                    <div class="meta-row">
                        <span class="meta-label">Currency:</span>
                        {{ $invoice->currency->code }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 35%">Description</th>
                    <th style="width: 20%">Policy/Details</th>
                    <th style="width: 10%" class="text-center">Qty</th>
                    <th style="width: 15%" class="text-right">Unit Price</th>
                    <th style="width: 15%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->invoiceItems as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            @if($item->insurance)
                                <strong>{{ $item->insurance->name }}</strong>
                                <br><small>Insurance Policy</small>
                            @elseif($item->insurance_name)
                                <strong>{{ $item->insurance_name }}</strong>
                                <br><small>Custom Insurance</small>
                            @elseif($item->product)
                                <strong>{{ $item->product->name }}</strong>
                                <br><small>Product</small>
                            @elseif($item->product_name)
                                <strong>{{ $item->product_name }}</strong>
                                <br><small>Custom Product</small>
                            @else
                                <strong>Item</strong>
                            @endif
                        </td>
                        <td>
                            @if($item->policy_number)
                                <strong>Policy #:</strong> {{ $item->policy_number }}<br>
                            @endif
                            @if($item->policy_start_date && $item->policy_end_date)
                                <small>{{ $item->policy_start_date->format('M d, Y') }} - {{ $item->policy_end_date->format('M d, Y') }}</small>
                            @endif
                            @if($item->premium_amount)
                                <br><small>Premium: {{ getCurrencyAmount($item->premium_amount, true) }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                        <td class="text-right">{{ getCurrencyAmount($item->price, true) }}</td>
                        <td class="text-right">{{ getCurrencyAmount($item->total, true) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals Section -->
        <div class="totals-section">
            <div class="totals-left">
                @if($invoice->note)
                    <div class="notes-section">
                        <div class="notes-title">Notes:</div>
                        <div class="notes-content">{{ $invoice->note }}</div>
                    </div>
                @endif
            </div>
            <div class="totals-right">
                <table class="totals-table">
                    <tr>
                        <td><strong>Subtotal:</strong></td>
                        <td class="text-right">{{ getCurrencyAmount($invoice->amount, true) }}</td>
                    </tr>
                    @if($invoice->discount > 0)
                        <tr>
                            <td>
                                <strong>Discount:</strong>
                                @if($invoice->discount_type == 1)
                                    (Fixed)
                                @elseif($invoice->discount_type == 2)
                                    ({{ $invoice->discount }}%)
                                @endif
                            </td>
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
                            <td><strong>Tax:</strong></td>
                            <td class="text-right">{{ getCurrencyAmount(array_sum($totalTax), true) }}</td>
                        </tr>
                    @endif
                    <tr class="total-row">
                        <td><strong>TOTAL:</strong></td>
                        <td class="text-right"><strong>{{ getCurrencyAmount($invoice->final_amount, true) }}</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Paid:</strong></td>
                        <td class="text-right">{{ getCurrencyAmount($paid ?? 0, true) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Due:</strong></td>
                        <td class="text-right">{{ getCurrencyAmount($dueAmount ?? ($invoice->final_amount - ($paid ?? 0)), true) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($invoice->term)
            <div class="notes-section">
                <div class="notes-title">Terms & Conditions:</div>
                <div class="notes-content">{{ $invoice->term }}</div>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your business!</p>
            <p>Generated on {{ now()->format('M d, Y \a\t H:i') }}</p>
        </div>
    </div>
</body>
</html>
