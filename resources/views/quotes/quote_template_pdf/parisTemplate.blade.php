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
            margin-bottom: 40px !important;
        }

        @if (getCurrencySymbol($quote->tenant_id) == '€')
            .euroCurrency {
                font-family: Arial, "Helvetica", Arial, "Liberation Sans", sans-serif;
            }
        @endif
    </style>
</head>

<body style="padding: 0rem;">
    <div class="preview-main client-preview paris-template">
        <div class="d" id="boxes">
            <div class="d-inner bg-img">
                <div class="position-relative" style="padding:0 1.5rem;">
                    <div class="bg-img" style="position:absolute; left:0; top:-40px;  min-width:220px;">
                        <img src="{{ asset('images/paris-bg-img.png') }}" class="w-100" alt="paris-bg-img" />
                    </div>
                    <div class="px-3" style="margin-top:-40px; z-index:2;">
                        <table class="w-100">
                            <tr>
                                <td style=" padding-right:8px">
                                    <div>
                                        <img src="{{ getLogoUrl($quote->tenant_id) }}" class="img-logo" alt="logo">
                                    </div>
                                </td>
                                <td class="heading-text" style="vertical-align:bottom; padding:1.5rem 0;">
                                    <div class="text-end">
                                        <h1 class="m-0" style="color:{{ $quote_template_color }}">
                                            {{ __('messages.quote.quote') }}</h1>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <table class="w-100">
                            <tr>
                                <td class="text-end">
                                    <p class="mb-1"><strong
                                            class="font-gray-900">{{ __('messages.quote.quote_id') . ':' }}
                                        </strong>
                                        <span class="font-gray-600"><b>#{{ $quote->quote_id }}</b></span>
                                    </p>
                                    <p class="mb-1"><strong
                                            class="font-gray-900">{{ __('messages.quote.quote_date') . ':' }}
                                        </strong>
                                        <span
                                            class="font-gray-600"><b>{{ \Carbon\Carbon::parse($quote->quote_date)->translatedFormat(currentDateFormat()) }}</b></span>
                                    </p>
                                    <p class=" mb-1"><strong
                                            class="font-gray-900">{{ __('messages.quote.due_date') . ':' }}
                                        </strong>
                                        <span
                                            class="font-gray-600"><b>{{ \Carbon\Carbon::parse($quote->due_date)->translatedFormat(currentDateFormat()) }}</b></span>
                                    </p>
                                </td>
                            </tr>
                        </table>
                        <div class="overflow-auto">
                            <table class="mt-4 w-100">
                                <tbody>
                                    <tr style="vertical-align:top;">
                                        <td width="50%" class="pe-3">
                                            <p class="mb-2" style="color:{{ $quote_template_color }}">
                                                <strong>{{ __('messages.common.from') . ':' }}&nbsp;</strong>
                                            </p>
                                            <p class="mb-1 font-black-900 fw-bold" style="white-space:nowrap;">
                                                <strong>{{ __('messages.common.name') . ':' }}&nbsp;</strong>{{ $setting['company_name'] }}
                                            </p>
                                            <p class="mb-1 font-black-900" style="max-width:220px;">
                                                <strong>{{ __('messages.common.address') . ':' }}&nbsp;</strong>
                                                {!! $setting['company_address'] !!}
                                            </p>
                                            <p class="mb-1 font-black-900" style="white-space:nowrap;">
                                                <strong>{{ __('messages.user.phone') . ':' }}</strong>
                                                {{ $setting['company_phone'] }}
                                            </p>
                                            @if (!empty($setting['gst_no']))
                                                <p class="mb-1 font-black-900 fw-bold">
                                                    <strong>{{ getVatNoLabel() . ':' }}</strong>
                                                    <span class="text-break font-gray-900">{{ $setting['gst_no'] }}
                                                    </span>
                                                </p>
                                            @endif
                                        </td>
                                        <td width="50%" class="pe-3">
                                            <p class="mb-2"
                                                style="white-space:nowrap;color:{{ $quote_template_color }}">
                                                <strong>{{ __('messages.common.to') . ':' }}</strong>
                                            </p>
                                            <p class="mb-1 font-black-900 fw-bold" style="white-space:nowrap;">
                                                <strong>{{ __('messages.common.name') . ':' }}&nbsp;</strong>{{ $client->user->full_name }}
                                            </p>
                                            <p class="mb-1 font-black-900 fw-bold" style="white-space:nowrap;">
                                                <strong>{{ __('messages.common.email') . ':' }}&nbsp;</strong>{{ $client->user->email }}
                                            </p>
                                            <p class="mb-1 font-black-900 fw-bold">
                                                <strong>{{ __('messages.common.address') . ':' }}&nbsp;</strong>{{ $client->address }}
                                            </p>
                                            @if (!empty($client->vat_no))
                                                <p class="mb-1 font-black-900 fw-bold">
                                                    <strong>{{ getVatNoLabel() . ':' }}</strong>
                                                    <span class="font-gray-900">{{ $client->vat_no }} </span>
                                                </p>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="overflow-auto w-100 mt-4">
                            <table class="invoice-table w-100">
                                <thead style="background-color: {{ $quote_template_color }}">
                                    <tr>
                                        <th class="p-10px fs-5"><b>#</b></th>
                                        <th class="p-10px fs-5 in-w-2"><b>{{ __('messages.product.product') }}</b></th>
                                        <th class="p-10px fs-5 text-center"><b>{{ __('messages.invoice.qty') }}</b>
                                        </th>
                                        <th class="p-10px fs-5 text-center text-nowrap">
                                            <b>{{ __('messages.product.unit_price') }}</b>
                                        </th>
                                        <th class="p-10px fs-5 text-center text-nowrap">
                                            <b>{{ __('messages.invoice.tax') . '(in %)' }}</b>
                                        </th>
                                        <th class="p-10px fs-5 text-end text-nowrap">
                                            <b>{{ __('messages.invoice.amount') }}</b>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($quote) && !empty($quote))
                                        @foreach ($quote->quoteItems as $key => $quoteItems)
                                            <tr>
                                                <td class="p-10px">
                                                    <span><b>{{ $key + 1 }}</b></span>
                                                </td>
                                                <td class="p-10px in-w-2">
                                                    <p class="mb-0 font-black-900">
                                                        <b>{{ isset($quoteItems->product->name) ? $quoteItems->product->name : $quoteItems->product_name ?? __('messages.common.n/a') }}</b>
                                                    </p>
                                                    @if (
                                                        !empty($quoteItems->product->description) &&
                                                            (isset($setting['show_product_description']) && $setting['show_product_description'] == 1))
                                                        <span
                                                            style="font-size: 12px; word-break: break-all">{{ $quoteItems->product->description }}</span>
                                                    @endif
                                                </td>
                                                <td class="p-10px font-gray-600 text-center">
                                                    {{ $quoteItems->quantity }}</td>
                                                <td class="p-10px font-gray-600 text-center tex-nowrap">
                                                    {{ isset($quoteItems->price) ? getCurrencyAmount($quoteItems->price, true) : __('messages.common.n/a') }}
                                                </td>
                                                <td class="p-10px font-gray-600 text-center">
                                                    @foreach ($quoteItems->quoteItemTax as $keys => $tax)
                                                        {{ $tax->tax ?? '--' }}
                                                        @if (!$loop->last)
                                                            ,
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td class="p-10px font-gray-600 text-end text-nowrap">
                                                    {{ isset($quoteItems->total) ? getCurrencyAmount($quoteItems->total, true) : __('messages.common.n/a') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            <table style="width:250px; margin-left:auto;">
                                <tbody style="border-bottom:1px solid #cecece">
                                    <tr>
                                        <td class="pb-2" style="color: {{ $quote_template_color }}">
                                            <strong>{{ __('messages.quote.amount') . ':' }}</strong>
                                        </td>
                                        <td class="text-end font-gray-600 pb-2 fw-bold">
                                            {{ getCurrencyAmount($quote->amount, true) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pb-2" style="color: {{ $quote_template_color }}">
                                            <strong>{{ __('messages.quote.discount') . ':' }}</strong>
                                        </td>
                                        <td class="text-end font-gray-600 pb-2 fw-bold">
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
                                                ($itemTaxesAmount * $quote->qouteTaxes->sum('value')) / 100;
                                            $totalTaxes = array_sum($totalTax) + $quoteTaxesAmount;
                                        @endphp
                                        <td class="pb-2" style="color:{{ $quote_template_color }}">
                                            <strong>{{ __('messages.invoice.tax') . ':' }}</strong>
                                        </td>
                                        <td class="text-end font-gray-600 pb-2 fw-bold">
                                            {!! numberFormat($totalTaxes) != 0
                                                ? '<span class="euroCurrency">' . getCurrencyAmount($totalTaxes, true) . '</span>'
                                                : __('messages.common.n/a') !!}
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="total-amount">
                                    <tr>
                                        <td class="py-2" style="color:{{ $quote_template_color }}">
                                            <strong>{{ __('messages.quote.total') . ':' }}</strong>
                                        </td>
                                        <td class="text-end py-2">
                                            {{ getCurrencyAmount($quote->final_amount, true) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="mt-5">
                            @if (!empty($quote->note))
                                <div class="mb-5 mt-sm-0 mt-2">
                                    <h6 class="font-gray-900 mb5"><b>{{ __('messages.client.notes') . ':' }}</b>
                                    </h6>
                                    <p class="font-gray-600">
                                        {!! nl2br($quote->note ?? __('messages.common.n/a')) !!}
                                    </p>
                                </div>
                            @endif
                            <table class="w-100">
                                <tr>
                                    @if (!empty($quote->term))
                                        <td class="w-75">
                                            <div>
                                                <h6 class="font-gray-900 mb5">
                                                    <b>{{ __('messages.invoice.terms') . ':' }}</b>
                                                </h6>
                                                <p class="font-gray-600 mb-0">
                                                    {!! nl2br($quote->term ?? __('messages.common.n/a')) !!}
                                                </p>
                                            </div>
                                        </td>
                                    @endif
                                    <td style="vertical-align:bottom; width:25%;" class="text-end">
                                        <div>
                                            <h6 class="mb5 pt-3">
                                                <b>{{ __('messages.setting.regards') . ':' }}</b>
                                            </h6>
                                            <p class="mb-0" style="color: {{ $quote_template_color }}">
                                                <b>{{ html_entity_decode(!empty($setting['app_name']) ? $setting['app_name'] : $userSetting['app_name']) }}</b>
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
