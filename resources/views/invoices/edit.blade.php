@extends('layouts.app')

@section('title')
    {{ __('Edit Invoice') }}
@endsection

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1>{{ __('Edit Invoice') }} #{{ $invoice->invoice_id }}</h1>
                        <div>
                            <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-eye"></i> {{ __('View') }}
                            </a>
                            <a href="{{ route('invoices.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <!-- Fixed form method to use PUT and proper AJAX submission -->
                {{ Form::model($invoice, ['route' => ['invoices.update', $invoice->id], 'method' => 'PUT', 'id' => 'invoiceEditForm']) }}
                @include('invoices.edit_fields')
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <!-- Hidden data for JavaScript -->
    <input type="hidden" id="insurances" value="{{ json_encode($associateInsurances) }}">
    <input type="hidden" id="taxes" value="{{ json_encode($associateTaxes) }}">
    <input type="hidden" id="currency" value="{{ getCurrencySymbol() }}">
    <input type="hidden" id="invoiceId" value="{{ $invoice->id }}">
<script src="{{ asset('assets/js/invoice-insurance.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date pickers with existing values
    $('#invoice_date, #due_date').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });

    // Initialize Select2 dropdowns
    $('.io-select2').select2();

    // Handle recurring status toggle
    const recurringToggle = document.getElementById('recurringStatusToggle');
    const recurringCycleContent = document.querySelector('.recurring-cycle-content');
    
    function toggleRecurringCycle() {
        if (recurringToggle.checked) {
            recurringCycleContent.style.display = 'block';
        } else {
            recurringCycleContent.style.display = 'none';
        }
    }
    
    recurringToggle.addEventListener('change', toggleRecurringCycle);
    toggleRecurringCycle(); // Initialize on page load

    // Handle form submission
    $('#saveAsDraft, #saveAndSend').on('click', function(e) {
        e.preventDefault();
        
        const status = $(this).data('status');
        const form = $('#invoiceEditForm');
        
        // Add status to form data
        if (form.find('input[name="form_status"]').length === 0) {
            form.append('<input type="hidden" name="form_status" value="' + status + '">');
        } else {
            form.find('input[name="form_status"]').val(status);
        }
        
        // Prepare form data
        const formData = new FormData(form[0]);
        
        // Show loading state
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
        
        // Submit via AJAX
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-HTTP-Method-Override': 'PUT'
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    toastr.success(response.message || 'Invoice updated successfully!');
                    
                    // Redirect to invoice list or show page
                    setTimeout(function() {
                        window.location.href = "{{ route('invoices.index') }}";
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Error updating invoice');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error updating invoice';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join(', ');
                }
                
                toastr.error(errorMessage);
                console.error('Update error:', xhr.responseText);
            },
            complete: function() {
                // Reset button state
                $('#saveAsDraft').prop('disabled', false).html('{{ __("Save Draft") }}');
                $('#saveAndSend').prop('disabled', false).html('{{ __("Update & Send") }}');
            }
        });
    });

    // Calculate totals when values change
    function calculateTotals() {
        let subtotal = 0;
        
        $('.tax-tr').each(function() {
            const quantity = parseFloat($(this).find('.qty').val()) || 0;
            const price = parseFloat($(this).find('.price').val()) || 0;
            const total = quantity * price;
            
            $(this).find('.amount-value').text(total.toFixed(2));
            subtotal += total;
        });
        
        $('#total').text(subtotal.toFixed(2));
        $('#total_amount').val(subtotal.toFixed(2));
        
        // Calculate discount
        const discount = parseFloat($('#discount').val()) || 0;
        const discountType = parseInt($('#discountType').val()) || 0;
        let discountAmount = 0;
        
        if (discountType === 0) { // Fixed amount
            discountAmount = discount;
        } else if (discountType === 1) { // Percentage
            discountAmount = (subtotal * discount) / 100;
        }
        
        $('#discountAmount').text(discountAmount.toFixed(2));
        
        // Calculate final amount
        const finalAmount = subtotal - discountAmount;
        $('#finalAmount').text(finalAmount.toFixed(2));
        $('#finalTotalAmt').val(finalAmount.toFixed(2));
    }
    
    // Bind calculation events
    $(document).on('input', '.qty, .price, #discount', calculateTotals);
    $(document).on('change', '#discountType', calculateTotals);
    
    // Initial calculation
    calculateTotals();

    // Handle insurance selection
    $(document).on('change', '.insurance', function() {
        const insuranceId = $(this).val();
        const row = $(this).closest('tr');
        
        if (insuranceId && insuranceId !== 'custom') {
            $.get("{{ route('invoices.get-insurance', '') }}/" + insuranceId)
                .done(function(response) {
                    if (response.success) {
                        row.find('.policy-number').val(response.data.policy_number);
                        row.find('.price').val(response.data.premium_amount);
                        calculateTotals();
                    } else {
                        toastr.error(response.message || 'Error loading insurance details');
                    }
                })
                .fail(function(xhr) {
                    console.log('[v0] Insurance AJAX error:', xhr.responseText);
                    toastr.error('Error loading insurance details. Please try again.');
                });
        } else {
            row.find('.policy-number').val('').prop('readonly', insuranceId !== 'custom');
            if (insuranceId === 'custom') {
                row.find('.policy-number').prop('readonly', false);
            }
        }
    });

    // Add new item row
    $('#addItem').on('click', function() {
        const newRowNumber = $('.tax-tr').length + 1;
        const newRow = `
            <tr class="tax-tr">
                <td class="text-center item-number align-center">${newRowNumber}</td>
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
                    <input type="number" name="quantity[]" class="form-control qty form-control-solid" value="1" required min="0" step="0.01">
                </td>
                <td>
                    <input type="number" name="price[]" class="form-control price-input price form-control-solid" value="0" min="0" step="0.01" required>
                </td>
                <td>
                    <select name="tax[]" class='form-select io-select2 fw-bold tax' data-control='select2' multiple="multiple">
                        @foreach ($taxes as $tax)
                            <option value="{{ $tax->value }}" data-id="{{ $tax->id }}">{{ $tax->name }}</option>
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
        `;
        
        $('#billTbl tbody').append(newRow);
        
        // Initialize Select2 for new row
        $('#billTbl tbody tr:last .io-select2').select2();
        
        calculateTotals();
    });

    // Delete item row
    $(document).on('click', '.delete-invoice-item', function() {
        if ($('.tax-tr').length > 1) {
            $(this).closest('tr').remove();
            
            // Renumber rows
            $('.tax-tr').each(function(index) {
                $(this).find('.item-number').text(index + 1);
            });
            
            calculateTotals();
        } else {
            toastr.warning('At least one item is required');
        }
    });
});
</script>
@endsection


