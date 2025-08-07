<div class="dropdown d-flex align-items-center my-3 my-sm-3 me-2" wire:ignore>
    <button class="btn btn btn-icon btn-primary text-white dropdown-toggle hide-arrow ps-2 pe-0" type="button"
        id="clientTransactionFilters" data-bs-auto-close="outside" data-bs-toggle="dropdown" aria-expanded="false">
        <p class="text-center">
            <i class='fas fa-filter'></i>
        </p>
    </button>
    <div class="dropdown-menu py-0" aria-labelledby="clientTransactionFilters">
        <div class="text-start border-bottom py-4 px-7">
            <h3 class="text-gray-900 mb-0">{{ __('messages.common.filter_options') }}</h3>
        </div>
        <div class="p-5">
            <div class="mb-5">
                <label class="form-label">{{ __('messages.invoice.payment_method') }}:</label>
                {{ Form::select('payment_mode', ['0' => 'All'] + \App\Models\Payment::PAYMENT_MODE, null, [
                    'class' => 'form-select',
                    'data-control' => 'select2',
                    'id' => 'paymentModeID',
                ]) }}
            </div>
            <div class="d-flex justify-content-end">
                <button type="reset" class="btn btn-secondary"
                    id="paymentModeResetFilters">{{ __('messages.common.reset') }}</button>
            </div>
        </div>
    </div>
</div>
<div class="dropdown my-3 my-sm-3">
    <button class="btn btn-success text-white dropdown-toggle" type="button" data-bs-toggle="dropdown"
        aria-expanded="false">
        {{ __('messages.common.export') }}
    </button>
    <ul class="dropdown-menu export-dropdown">
        <a href="{{ route('client.transactionsExcel') }}" class="dropdown-item" >
            <i class="fas fa-file-excel me-1"></i> {{ __('messages.invoice.excel_export') }}
        </a>
        <a href="{{ route('client.export.transactions.pdf') }}" class="dropdown-item" >
            <i class="fas fa-file-pdf me-1"></i> {{ __('messages.pdf_export') }}
        </a>
    </ul>
</div>
