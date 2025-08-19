<div class="row">
   <div class="col-lg-4 col-sm-12 mb-5">
    {{ Form::label('client_id', __('Client') . ':', ['class' => 'form-label required mb-3']) }}

    <div class="input-group" data-bs-toggle="tooltip" data-bs-trigger="hover" title="{{ __('Select Client') }}">
        {{ Form::select('client_id', $clients, old('client_id', $invoice->client_id ?? null), [
            
        ]) }}
    </div>
</div>


    <div class="col-lg-4 col-sm-12 mb-lg-0 mb-5">
        @if (!empty(getSettingValue('invoice_no_prefix')) || !empty(getSettingValue('invoice_no_suffix')))
            <div class="" data-bs-toggle="tooltip" data-bs-trigger="hover" title="" data-bs-original-title="invoice number">
                <div class="form-group col-sm-12 mb-5">
                    {{ Form::label('paid_amount', __('Invoice Number') . ':', ['class' => 'form-label mb-3 required']) }}
                    <div class="input-group">
                        @if (!empty(getSettingValue('invoice_no_prefix')))
                            <a class="input-group-text bg-secondary border-0 text-decoration-none text-black" data-toggle="tooltip" data-placement="right" title="Invoice No Prefix">
                                {{ getSettingValue('invoice_no_prefix') }}
                            </a>
                        @endif
                        {{ Form::text('invoice_id', $invoice->invoice_id ?? null, ['class' => 'form-control', 'required', 'id' => 'invoiceId', 'maxlength' => 6, 'onkeypress' => 'return blockSpecialChar(event)']) }}
                        @if (!empty(getSettingValue('invoice_no_suffix')))
                            <a class="input-group-text bg-secondary border-0 text-decoration-none text-black" data-toggle="tooltip" data-placement="right" title="Invoice No Suffix">
                                {{ getSettingValue('invoice_no_suffix') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="" data-bs-toggle="tooltip" data-bs-trigger="hover" title="" data-bs-original-title="{{ __('Invoice Number') }}">
                <span class="form-label">{{ __('Invoice') }} #</span>
                {{ Form::text('invoice_id', $invoice->invoice_id ?? null, ['class' => 'form-control mt-3', 'required', 'id' => 'invoiceId', 'maxlength' => 6, 'onkeypress' => 'return blockSpecialChar(event)']) }}
            </div>
        @endif
    </div>
    <div class="col-lg-4 col-sm-12 mb-5">
        {{ Form::label('invoice_date', __('Invoice Date') . ':', ['class' => 'form-label required mb-3']) }}
        {{ Form::date('invoice_date', isset($invoice) ? $invoice->invoice_date->format('Y-m-d') : date('Y-m-d'), ['class' => 'form-control', 'id' => 'invoice_date', 'required']) }}
    </div>

    <div class="mb-5 col-lg-4 col-sm-12">
        {{ Form::label('due_date', __('Due Date') . ':', ['class' => 'form-label required mb-3']) }}
        {{ Form::date('due_date', isset($invoice) ? $invoice->due_date->format('Y-m-d') : date('Y-m-d', strtotime('+30 days')), ['class' => 'form-control', 'id' => 'due_date', 'required']) }}
    </div>
    <div class="col-lg-4 col-sm-12">
        <div class="mb-5">
            {{ Form::label('status', __('Status') . ':', ['class' => 'form-label required mb-3']) }}
            {{ Form::select('status', getTranslatedData($statusArr), $invoice->status ?? null, ['class' => 'form-select io-select2', 'id' => 'status', 'required', 'data-control' => 'select2']) }}
        </div>
    </div>
    <div class="mb-5 col-lg-4 col-sm-12">
        {{ Form::label('templateId', __('Invoice Template') . ':', ['class' => 'form-label mb-3']) }}
        {{ Form::select('template_id', $template, $invoice->template_id ?? getInvoiceTemplateId(), ['class' => 'form-select io-select2', 'id' => 'templateId', 'required', 'data-control' => 'select2']) }}
    </div>
    <div class="mb-5 col-lg-4 col-sm-12">
        {{ Form::label('payment_qr_code_id', __('Payment QR Code') . ':', ['class' => 'form-label mb-3']) }}
        {{ Form::select('payment_qr_code_id', $paymentQrCodes, $invoice->payment_qr_code_id ?? $defaultPaymentQRCode, ['class' => 'form-select io-select2 payment-qr-code', 'data-control' => 'select2', 'placeholder' => __('Select Payment QR Code')]) }}
    </div>
    <div class="mb-5 col-lg-4 col-sm-12">
        {{ Form::label('invoiceCurrencyType', __('Currency') . ':', ['class' => 'form-label mb-3']) }}
        <select id="invoiceCurrencyType" class="form-select" name="currency_id">
            <option value="">{{ __('Select Currency') }}</option>
            @foreach ($currencies as $key => $currency)
                <option value="{{ $currency['id'] }}" {{ ($invoice->currency_id ?? null) == $currency['id'] ? 'selected' : '' }}>
                    {{ $currency['icon'] }} &nbsp;&nbsp;&nbsp; {{ $currency['name'] }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="mb-5 col-lg-4 col-sm-12 mt-8">
        <label class="form-check form-switch form-check-custom mt-3">
            <input class="form-check-input recurring-status" type="checkbox" name="recurring_status" id="recurringStatusToggle" {{ ($invoice->recurring_status ?? false) ? 'checked' : '' }}>
            <span class="form-check-label text-gray-600" for="recurringStatusToggle">{{ __('This is recurring invoice') }}</span>&nbsp;&nbsp;
        </label>
    </div>
    <div class="mb-5 col-lg-4 col-sm-12 recurring-cycle-content">
        {{ Form::label('recurringCycle', __('Recurring Cycle') . ':', ['class' => 'form-label mb-3']) }}
        {{ Form::number('recurring_cycle', $invoice->recurring_cycle ?? null, ['class' => 'form-control', 'id' => 'recurringCycle', 'autocomplete' => 'off', 'placeholder' => __('Recurring Days'), 'oninput' => "validity.valid||(value=value.replace(/[e\+\-]/gi,''))"]) }}
    </div>
    <div class="col-12 text-end my-5">
        <button type="button" class="btn btn-primary text-start" id="addItem">
            {{ __('Add Insurance') }}</button>
    </div>
    <div class="table-responsive">
        <table class="table table-striped box-shadow-none mt-4" id="billTbl">
            <thead>
                <tr class="border-bottom fs-7 fw-bolder text-gray-700 text-uppercase">
                    <th scope="col">#</th>
                    <th scope="col" class="required">{{ __('Insurance/Product') }}</th>
                    <th scope="col" class="required">{{ __('Policy Number') }}</th>
                    <th scope="col" class="required">{{ __('Qty') }}</th>
                    <th scope="col" class="required">{{ __('Unit Price') }}</th>
                    <th scope="col">{{ __('Tax') }}</th>
                    <th scope="col" class="required">{{ __('Amount') }}</th>
                    <th scope="col" class="text-end">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody class="invoice-item-container">
                @if(isset($invoice) && $invoice->invoiceItems->count() > 0)
                    @foreach($invoice->invoiceItems as $index => $item)
                        <tr class="tax-tr">
                            <td class="text-center item-number align-center">{{ $index + 1 }}</td>
                            <td class="table__item-desc w-25">
                                <select name="insurance_id[]" class="form-select insurance io-select2" required data-control="select2">
                                    <option value="">{{ __('Select Insurance') }}</option>
                                    @foreach ($insurances as $key => $insurance)
                                        <option value="{{ $key }}" {{ $item->insurance_id == $key ? 'selected' : '' }}>{{ $insurance }}</option>
                                    @endforeach
                                    <option value="custom" {{ $item->insurance_id == 'custom' ? 'selected' : '' }}>{{ __('Enter Custom Item') }}</option>
                                </select>
                            </td>
                            <td class="table__policy">
                                <input type="text" name="policy_number[]" class="form-control policy-number form-control-solid" placeholder="{{ __('Policy Number') }}" value="{{ $item->policy_number ?? '' }}" readonly>
                            </td>
                            <td class="table__qty">
                                {{ Form::number('quantity[]', $item->quantity ?? 1, ['class' => 'form-control qty form-control-solid', 'required', 'min' => '0', 'step' => '0.01']) }}
                            </td>
                            <td>
                                {{ Form::number('price[]', $item->price ?? 0, ['class' => 'form-control price-input price form-control-solid', 'min' => '0', 'step' => '0.01', 'required']) }}
                            </td>
                            <td>
                                <select name="tax[]" class='form-select io-select2 fw-bold tax' data-control='select2' multiple="multiple">
                                    @foreach ($taxes as $tax)
                                        @php
                                            $isSelected = false;
                                            if(isset($item->invoiceItemTax)) {
                                                foreach($item->invoiceItemTax as $itemTax) {
                                                    if($itemTax->tax_id == $tax->id) {
                                                        $isSelected = true;
                                                        break;
                                                    }
                                                }
                                            }
                                        @endphp
                                        <option value="{{ $tax->value }}" data-id="{{ $tax->id }}" {{ $isSelected ? 'selected' : '' }}>{{ $tax->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="text-end item-total pt-8 text-nowrap">
                                @if (!getSettingValue('currency_after_amount'))
                                    <span>{{ getCurrencySymbol() }}</span>
                                @endif
                                <span class="amount-value">{{ number_format(($item->quantity ?? 1) * ($item->price ?? 0), 2) }}</span>
                                @if (getSettingValue('currency_after_amount'))
                                    <span>{{ getCurrencySymbol() }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button type="button" title="{{ __('Delete') }}" class="btn btn-icon fs-3 text-danger btn-active-color-danger delete-invoice-item">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr class="tax-tr">
                        <td class="text-center item-number align-center">1</td>
                        <td class="table__item-desc w-25">
                            <select name="insurance_id[]" class="form-select insurance io-select2" required data-control="select2">
                                <option value="">{{ __('Select Insurance') }}</option>
                                @foreach ($insurances as $key => $insurance)
                                    <option value="{{ $key }}">{{ $insurance }}</option>
                                @endforeach
                                <option value="custom">{{ __('Enter Custom Item') }}</option>
                            </select>
                        </td>
                        <td class="table__policy">
                            <input type="text" name="policy_number[]" class="form-control policy-number form-control-solid" placeholder="{{ __('Policy Number') }}" readonly>
                        </td>
                        <td class="table__qty">
                            {{ Form::number('quantity[]', 1, ['class' => 'form-control qty form-control-solid', 'required', 'min' => '0', 'step' => '0.01']) }}
                        </td>
                        <td>
                            {{ Form::number('price[]', 0, ['class' => 'form-control price-input price form-control-solid', 'min' => '0', 'step' => '0.01', 'required']) }}
                        </td>
                        <td>
                            <select name="tax[]" class='form-select io-select2 fw-bold tax' data-control='select2' multiple="multiple">
                                @foreach ($taxes as $tax)
                                    <option value="{{ $tax->value }}" data-id="{{ $tax->id }}" {{ $defaultTax == $tax->id ? 'selected' : '' }}>{{ $tax->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-end item-total pt-8 text-nowrap">
                            @if (!getSettingValue('currency_after_amount'))
                                <span>{{ getCurrencySymbol() }}</span>
                            @endif
                            <span class="amount-value">0.00</span>
                            @if (getSettingValue('currency_after_amount'))
                                <span>{{ getCurrencySymbol() }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <button type="button" title="{{ __('Delete') }}" class="btn btn-icon fs-3 text-danger btn-active-color-danger delete-invoice-item">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    
    
    <div class="row">
        <div class="col-lg-7 col-sm-12 mt-2 mt-lg-0 align-right-for-full-screen">
            <div class="mb-2 col-xl-5 col-lg-8 col-sm-12 float-right">
                <label class="form-check form-switch form-check-custom mt-3">
                    <span class="form-check-label text-gray-600" for="invoicesDiscounBeforeTax">{{ 'Discount %(applied before tax):' }}</span>
                    <input class="form-check-input" type="checkbox" name="discount_before_tax" id="invoicesDiscounBeforeTax" disabled>
                    &nbsp;&nbsp;
                </label>
            </div>
            <div class="mb-2 col-xl-5 col-lg-8 col-sm-12 float-right">
                {{ Form::label('discount', __('Discount') . ':', ['class' => 'form-label mb-1']) }}
                <div class="input-group">
                    {{ Form::number('discount', $invoice->discount ?? 0, ['id' => 'discount', 'class' => 'form-control ', 'oninput' => "validity.valid||(value=value.replace(/[e\+\-]/gi,''))", 'min' => '0', 'step' => '.01', 'pattern' => "^\d*(\.\d{0,2})?$"]) }}
                    <div class="input-group-append" style="width: 210px !important;">
                        {{ Form::select('discount_type', getTranslatedData($discount_type), $invoice->discount_type ?? 0, ['class' => 'form-select io-select2 discount-type', 'id' => 'discountType', 'data-control' => 'select2']) }}
                    </div>
                </div>
            </div>
            <div class="mb-2 col-xl-5 col-lg-8 col-sm-12 float-right">
                {{ Form::label('tax2', __('Tax') . ':', ['class' => 'form-label mb-1']) }}
                <select name="taxes[]" class='form-select io-select2 fw-bold invoice-taxes' data-control='select2' multiple="multiple">
                    @foreach ($taxes as $tax)
                        @php
                            $isInvoiceTaxSelected = false;
                            if(isset($selectedInvoiceTaxes) && in_array($tax->id, $selectedInvoiceTaxes)) {
                                $isInvoiceTaxSelected = true;
                            }
                        @endphp
                        <option value="{{ $tax->id }}" data-tax="{{ $tax->value }}" {{ $isInvoiceTaxSelected ? 'selected' : '' }}>{{ $tax->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-xxl-3 col-lg-5 col-md-6 ms-md-auto mt-4 mb-lg-10 mb-6">
            <div class="border-top">
                <table class="table table-borderless box-shadow-none mb-0 mt-5">
                    <tbody>
                        <tr>
                            <td class="ps-0">{{ __('Sub Total') . ':' }}</td>
                            <td class="text-gray-900 text-end pe-0">
                                @if (!getSettingValue('currency_after_amount'))
                                    <span>{{ getCurrencySymbol() }}</span>
                                @endif 
                                <span id="total" class="price">{{ number_format($invoice->amount ?? 0, 2) }}</span>
                                @if (getSettingValue('currency_after_amount'))
                                    <span>{{ getCurrencySymbol() }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-0">{{ __('Discount') . ':' }}</td>
                            <td class="text-gray-900 text-end pe-0">
                                @if (!getSettingValue('currency_after_amount'))
                                    <span>{{ getCurrencySymbol() }}</span>
                                @endif 
                                <span id="discountAmount">{{ number_format($invoice->discount ?? 0, 2) }}</span>
                                @if (getSettingValue('currency_after_amount'))
                                    <span>{{ getCurrencySymbol() }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-0">{{ __('Total Tax') . ':' }}</td>
                            <td class="text-gray-900 text-end pe-0">
                                @if (!getSettingValue('currency_after_amount'))
                                    <span>{{ getCurrencySymbol() }}</span>
                                @endif 
                                <span id="totalTax">0</span>
                                @if (getSettingValue('currency_after_amount'))
                                    <span>{{ getCurrencySymbol() }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-0">{{ __('Total') . ':' }}</td>
                            <td class="text-gray-900 text-end pe-0">
                                @if (!getSettingValue('currency_after_amount'))
                                    <span>{{ getCurrencySymbol() }}</span>
                                @endif 
                                <span id="finalAmount">{{ number_format($invoice->final_amount ?? 0, 2) }}</span>
                                @if (getSettingValue('currency_after_amount'))
                                    <span>{{ getCurrencySymbol() }}</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row justify-content-left">
        <div class="col-lg-12 col-md-12 col-sm-12 end justify-content-left mb-5">
            <button type="button" class="btn btn-primary note" id="addNote">
                <i class="fas fa-plus"></i> {{ __('Add Note & Terms') }}
            </button>
            <button type="button" class="btn btn-danger note" id="removeNote">
                <i class="fas fa-minus"></i> {{ __('Remove Note & Terms') }}
            </button>
        </div>
        <div class="col-lg-6 mb-5 mt-5" id="noteAdd">
            {{ Form::label('note', __('Note') . ':', ['class' => 'form-label fs-6 fw-bolder text-gray-700 mb-3', 'rows' => '5']) }}
            {{ Form::textarea('note', $invoice->note ?? null, ['class' => 'form-control', 'id' => 'note']) }}
        </div>
        <div class="col-lg-6 mb-5 mt-5" id="termRemove">
            {{ Form::label('term', __('Terms') . ':', ['class' => 'form-label fs-6 fw-bolder text-gray-700 mb-3', 'rows' => '5']) }}
            {{ Form::textarea('term', $invoice->term ?? null, ['class' => 'form-control', 'id' => 'term']) }}
        </div>
    </div>
</div>

<!-- Hidden Fields -->
{{ Form::hidden('amount', $invoice->amount ?? 0, ['class' => 'form-control', 'id' => 'total_amount']) }}
{{ Form::hidden('final_amount', $invoice->final_amount ?? 0, ['class' => 'form-control', 'id' => 'finalTotalAmt']) }}

<!-- Submit Buttons -->
<div class="float-end">
    <div class="form-group col-sm-12">
        <button type="button" name="draft" class="btn btn-primary mx-1 ms-ms-3 mb-3 mb-sm-0" id="saveAsDraft" data-status="0" value="0">{{ __('Save Draft') }}</button>
        <button type="button" name="save" class="btn btn-primary mx-1 ms-ms-3 mb-3 mb-sm-0" id="saveAndSend" data-status="1" value="1">{{ __('Update & Send') }}</button>
        <a href="{{ route('invoices.index') }}" class="btn btn-secondary btn-active-light-primary">{{ __('Cancel') }}</a>
    </div>
</div>
