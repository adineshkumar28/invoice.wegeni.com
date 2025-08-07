<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link rel="icon" href="{{ asset('web/media/logos/favicon.ico') }}" type="image/png">
    <title>{{ __('messages.quote.quote_pdf') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/invoice-pdf.css') }}" rel="stylesheet" type="text/css" />
    <style>
        * {
            font-family: DejaVu Sans, Arial, "Helvetica", Arial, "Liberation Sans", sans-serif;
        }

        @page {
            margin-top: 40px !important;
        }

        @if (getCurrencySymbol($quote->tenant_id) == '€')
            .euroCurrency {
                font-family: Arial, "Helvetica", Arial, "Liberation Sans", sans-serif;
            }
        @endif
    </style>
</head>

<body style="padding: 0rem;">
    <div class="preview-main client-preview istanbul-template">
        <div class="d" id="boxes">
            <div class="d-inner">
                <div class="position-relative" style="padding:0 1.5rem;">
                    <div class="bg-img" style="position:absolute; left:0; top:-40px;">
                        <img src="{{ asset('images/istanbul-bg-img.png') }}" alt="istanbul-bg-img" />
                    </div>
                    <div class="invoice-header px-3" style="z-index:2; margin-top:-25px;">
                        <table class="overflow-hidden w-100">
                            <tr>
                                <td class="heading-text pb-2 pe-10 w-30">
                                    <div class="position-relative z-10">
                                        <h1 class="m-0 text-white"
                                            style="font-size: 32px; font-weight:700; letter-spacing:2px">
                                            {{ __('messages.quote.quote_name') }}
                                        </h1>
                                    </div>
                                </td>
                                <td class="text-end" style="padding-top: 1.5rem;">
                                    <div>
                                        <img src="{{ getLogoUrl($quote->tenant_id) }}" class="img-logo" alt="logo">
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <div class="px-3">
                            <div class="my-4 text-end ml-auto">
                                <p class="mb-1 fs-6">
                                    <span class="font-gray-900"><b>{{ __('messages.quote.quote_id') . ':' }}</b></span>
                                    <span class="font-gray-600">
                                        <b>#{{ $quote->quote_id }}</b>
                                    </span>
                                </p>
                                <p class="mb-1 fs-6">
                                    <span
                                        class="font-gray-900"><b>{{ __('messages.quote.quote_date') . ':' }}</b></span>
                                    <span class="font-gray-600">
                                        <b>{{ \Carbon\Carbon::parse($quote->quote_date)->translatedFormat(currentDateFormat()) }}</b>
                                    </span>
                                </p>
                                <p class="fs-6">
                                    <span class="font-gray-900"><b>{{ __('messages.quote.due_date') . ':' }}</b></span>
                                    <span class="font-gray-600">
                                        <b>{{ \Carbon\Carbon::parse($quote->due_date)->translatedFormat(currentDateFormat()) }}</b>
                                    </span>
                                </p>
                            </div>
                            <table class="mb-3 w-100">
                                <tbody>
                                    <tr style="vertical-align:top;">
                                        <td width="33%;" class="pe-15">
                                            <p class="fs-6 mb-2 font-gray-900">
                                                <b>{{ __('messages.common.from') . ':' }}</b>
                                            </p>
                                            <p class=" mb-1  fs-6">
                                                <span
                                                    class="font-gray-600">{{ __('messages.common.name') . ':' }}</span>
                                                {{ $setting['company_name'] }}
                                            </p>
                                            <p class=" mb-1 fw-bold fs-6">
                                                <span
                                                    class="font-gray-600">{{ __('messages.common.address') . ':' }}</span>
                                                {!! $setting['company_address'] !!}
                                            </p>
                                            <p class=" mb-1 fw-bold fs-6">
                                                <span
                                                    class="font-gray-600">{{ __('messages.user.phone') . ':' }}</span>
                                                {{ $setting['company_phone'] }}
                                            </p>
                                            @if (!empty($setting['gst_no']))
                                                <p class="mb-1 fs-6">
                                                    <span class="font-gray-600">{{ getVatNoLabel() . ':' }}</span>
                                                    {{ $setting['gst_no'] }}
                                                </p>
                                            @endif
                                        </td>
                                        <td width="35%;" class="ps-5rem">
                                            <p class="fs-6 mb-2 font-gray-900">
                                                <b>{{ __('messages.common.to') . ':' }}</b>
                                            </p>
                                            <p class=" mb-1  fs-6">
                                                <span
                                                    class="font-gray-600">{{ __('messages.common.name') . ':' }}</span>
                                                {{ $client->user->full_name }}
                                            </p>
                                            <p class="mb-1 fs-6">
                                                <span
                                                    class="font-gray-600">{{ __('messages.common.email') . ':' }}</span>
                                                {{ $client->user->email }}
                                            </p>
                                            <p class="mb-1  fs-6">
                                                <span
                                                    class="font-gray-600">{{ __('messages.common.address') . ':' }}</span>
                                                {{ $client->address }}
                                            </p>
                                            @if (!empty($client->vat_no))
                                                <p class="mb-1 fs-6">
                                                    <span class="font-gray-600">{{ getVatNoLabel() . ':' }}</span>
                                                    {{ $client->vat_no }}
                                                </p>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="px-3">
                            <table class="invoice-table w-100">
                                <thead style="background-color: {{ $quote_template_color }};">
                                    <tr>
                                        <th class="p-2 fs-5" style="width:5% !important;">#</th>
                                        <th class="p-2 fs-5 in-w-2">{{ __('messages.product.product') }}</th>
                                        <th class="p-2 fs-5 text-center" style="width:9% !important;">
                                            {{ __('messages.invoice.qty') }}</th>
                                        <th class="p-2 fs-5 text-center  text-nowrap" style="width:18% !important;">
                                            {{ __('messages.product.unit_price') }}</th>
                                        <th class="p-2 fs-5 text-center text-nowrap" style="width:13% !important;">
                                            {{ __('messages.invoice.tax') . '(in %)' }}</th>
                                        <th class="p-2 fs-5 text-end  text-nowrap" style="width:17% !important;">
                                            {{ __('messages.invoice.amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($quote) && !empty($quote))
                                        @foreach ($quote->quoteItems as $key => $quoteItems)
                                            <tr>
                                                <td class="p-2" style="width:5%; vertical-align:top;">
                                                    <span>{{ $key + 1 }}</span>
                                                </td>
                                                <td class="p-2 in-w-2" style="vertical-align:top;">
                                                    <p class="fw-bold mb-0">
                                                        {{ isset($quoteItems->product->name) ? $quoteItems->product->name : $quoteItems->product_name ?? __('messages.common.n/a') }}
                                                    </p>
                                                    @if (
                                                        !empty($quoteItems->product->description) &&
                                                            (isset($setting['show_product_description']) && $setting['show_product_description'] == 1))
                                                        <span
                                                            style="font-size: 12px; word-break: break-all">{{ $quoteItems->product->description }}</span>
                                                    @endif
                                                </td>
                                                <td class="p-2 text-center text-nowrap font-gray-600"
                                                    style="width:9% !important; vertical-align:top;">
                                                    {{ $quoteItems->quantity }}</td>
                                                <td class="p-2 text-center text-nowrap euroCurrency font-gray-600"
                                                    style="width:15% !important; vertical-align:top;">
                                                    {{ isset($quoteItems->price) ? getCurrencyAmount($quoteItems->price, true) : __('messages.common.n/a') }}
                                                </td>
                                                <td class="p-2 text-center text-nowrap font-gray-600"
                                                    style="width:13% !important; vertical-align: top;">
                                                    @foreach ($quoteItems->quoteItemTax as $keys => $tax)
                                                        {{ $tax->tax ?? '--' }}
                                                        @if (!$loop->last)
                                                            ,
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td class="p-2 text-end text-nowrap euroCurrency font-gray-600"
                                                    style="width:14% !important; vertical-align:top;">
                                                    {{ isset($quoteItems->total) ? getCurrencyAmount($quoteItems->total, true) : __('messages.common.n/a') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            <div class="my-10 mt-5">
                                <table class="w-100">
                                    <tr>
                                        <td style="width: 60% !important;">
                                        </td>
                                        <td style="width: 40% !important;">
                                            <table class="w-100">
                                                <tbody>
                                                    <tr>
                                                        <td class="py-1 px-2 font-orange text-nowrap"
                                                            style="color:{{ $quote_template_color }}">
                                                            <strong>{{ __('messages.quote.amount') . ':' }}</strong>
                                                        </td>
                                                        <td
                                                            class="text-end font-gray-600 py-1 px-2 fw-bold text-nowrap euroCurrency">
                                                            {{ getCurrencyAmount($quote->amount, true) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="py-1 px-2 font-orange text-nowrap"
                                                            style="color:{{ $quote_template_color }}">
                                                            <strong>{{ __('messages.quote.discount') . ':' }}</strong>
                                                        </td>
                                                        <td
                                                            class="text-end font-gray-600 py-1 px-2 fw-bold text-nowrap">
                                                            @if ($quote->discount == 0)
                                                                <span>{{ __('messages.common.n/a') }}</span>
                                                            @else
                                                                @if (isset($quote) && $quote->discount_type == \App\Models\Quote::FIXED)
                                                                    <span
                                                                        class="euroCurrency">{{ isset($quote->discount) ? getCurrencyAmount($quote->discount, true) : __('messages.common.n/a') }}</span>
                                                                @else
                                                                    {{ $quote->discount }}<span
                                                                        style="font-family: DejaVu Sans">&#37;</span>
                                                                @endif
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        @php
                                                            $itemTaxesAmount = $quote->amount + array_sum($totalTax);
                                                            $quoteTaxesAmount =
                                                                ($itemTaxesAmount * $quote->qouteTaxes->sum('value')) /
                                                                100;
                                                            $totalTaxes = array_sum($totalTax) + $quoteTaxesAmount;
                                                        @endphp
                                                        <td class="py-1 px-2 font-orange text-nowrap"
                                                            style="color:
                                        {{ $quote_template_color }}">
                                                            <strong>{{ __('messages.invoice.tax') . ':' }}</strong>
                                                        </td>
                                                        <td
                                                            class="text-end font-gray-600 py-1 px-2 fw-bold text-nowrap">
                                                            {!! numberFormat($totalTaxes) != 0
                                                                ? '<span class="euroCurrency">' . getCurrencyAmount($totalTaxes, true) . '</span>'
                                                                : __('messages.common.n/a') !!}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot class="border-top-gray">
                                                    <tr>
                                                        <td class="p-2 font-orange text-nowrap"
                                                            style="color:{{ $quote_template_color }}">
                                                            <strong>
                                                                {{ __('messages.quote.total') . ':' }}</strong>
                                                        </td>
                                                        <td
                                                            class="text-end font-gray-900 p-2 text-nowrap euroCurrency">
                                                            {{ getCurrencyAmount($quote->final_amount, true) }}
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="mt-5">
                                @if (!empty($quote->note))
                                    <div class="mb-5">
                                        <p class="fw-6 mb5"><b>{{ __('messages.client.notes') . ':' }}</b></p>
                                        <p class="font-gray-600">{!! nl2br($quote->note ?? __('messages.common.n/a')) !!}
                                        </p>
                                    </div>
                                @endif
                                @if (!empty($quote->term))
                                    <div class="w-75 mb-8">
                                        <p class="fw-6 mb5"><b>{{ __('messages.invoice.terms') . ':' }}</b>
                                        </p>
                                        <p class="font-gray-600">{!! nl2br($quote->term ?? __('messages.common.n/a')) !!} </p>
                                    </div>
                                @endif
                                <div class="w-25 text-end" style="margin-left:auto; position: relative; top:-40px;">
                                    <p class="mb-0" style="color:{{ $quote_template_color }}">
                                        <b>{{ __('messages.common.regards') . ':' }}</b>
                                    </p>
                                    <p class="fs-6 font-gray-600">
                                        {{ html_entity_decode(!empty($setting['app_name']) ? $setting['app_name'] : $userSetting['app_name']) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
