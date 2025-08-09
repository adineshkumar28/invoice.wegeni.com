<div class="row gx-10 mb-5">
    <!-- Basic Insurance Information -->
    <div class="col-lg-6">
        <div class="mb-5">
            {{ Form::label('name', __('Insurance Name').':', ['class' => 'form-label required mb-3']) }}
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-shield-alt"></i>
                </span>
                {{ Form::text('name', $insurance->name, ['class' => 'form-control form-control-solid', 'placeholder' => __('Insurance Name'), 'required']) }}
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="mb-5">
            {{ Form::label('policy_number', __('Policy Number').':', ['class' => 'form-label required mb-3']) }}
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-file-contract"></i>
                </span>
                {{ Form::text('policy_number', $insurance->policy_number, ['class' => 'form-control form-control-solid', 'placeholder' => __('Policy Number'), 'required']) }}
            </div>
        </div>
    </div>

    <!-- Client Selection with Details -->
    <div class="col-lg-6">
        <div class="mb-5">
            {{ Form::label('client_id', __('Client').':', ['class' => 'form-label required mb-3']) }}
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-user"></i>
                </span>
                {{ Form::select('client_id', $clients, $insurance->client_id, ['class' => 'form-select form-select-solid', 'placeholder' => __('Select Client'), 'required', 'id' => 'clientSelect', 'data-control' => 'select2']) }}
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="mb-5">
            {{ Form::label('category_id', __('Insurance Category').':', ['class' => 'form-label required mb-3']) }}
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-tags"></i>
                </span>
                {{ Form::select('category_id', $categories, $insurance->category_id, ['class' => 'form-select form-select-solid', 'placeholder' => __('Select Category'), 'required', 'id' => 'categorySelect', 'data-control' => 'select2']) }}
            </div>
        </div>
    </div>

    <!-- Client Details Display -->
    <div class="col-lg-12" id="clientDetailsSection" style="display: none;">
        <div class="card bg-light-info mb-5">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-info-circle text-info fs-2 me-3"></i>
                    <h5 class="card-title mb-0">Client Details</h5>
                </div>
                <div class="row" id="clientDetails">
                    <!-- Client details will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Premium and Dates -->
    <div class="col-lg-4">
        <div class="mb-5">
            {{ Form::label('premium_amount', __('Premium Amount').':', ['class' => 'form-label required mb-3']) }}
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-dollar-sign"></i>
                </span>
                {{ Form::number('premium_amount', $insurance->premium_amount, ['class' => 'form-control form-control-solid', 'placeholder' => __('Premium Amount'), 'min' => '0', 'step' => '0.01', 'required']) }}
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="mb-5">
            {{ Form::label('start_date', __('Start Date').':', ['class' => 'form-label required mb-3']) }}
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-calendar-alt"></i>
                </span>
                {{ Form::date('start_date', $insurance->start_date->format('Y-m-d'), ['class' => 'form-control form-control-solid', 'required', 'id' => 'startDate']) }}
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="mb-5">
            {{ Form::label('end_date', __('End Date').':', ['class' => 'form-label required mb-3']) }}
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-calendar-times"></i>
                </span>
                {{ Form::date('end_date', $insurance->end_date->format('Y-m-d'), ['class' => 'form-control form-control-solid', 'required', 'id' => 'endDate']) }}
            </div>
            <div class="form-text" id="dateValidation"></div>
        </div>
    </div>

    <!-- Description -->
    <div class="col-lg-12">
        <div class="mb-5">
            {{ Form::label('description', __('Description').':', ['class' => 'form-label mb-3']) }}
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-align-left"></i>
                </span>
                {{ Form::textarea('description', $insurance->description, ['class' => 'form-control form-control-solid', 'rows' => '4', 'placeholder' => __('Insurance Description')]) }}
            </div>
        </div>
    </div>

    <!-- Custom Fields Section -->
    <div class="col-lg-12">
        <div class="separator separator-dashed my-10"></div>
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h3 class="mb-1">
                    <i class="fas fa-cogs text-primary me-2"></i>
                    Custom Fields
                </h3>
                <p class="text-muted mb-0">Add additional information specific to this insurance policy</p>
            </div>
            <button type="button" class="btn btn-success" id="addCustomField">
                <i class="fas fa-plus"></i> Add Custom Field
            </button>
        </div>
        
        <div id="customFieldsContainer">
            @if($insurance->custom_fields && count($insurance->custom_fields) > 0)
                @foreach($insurance->custom_fields as $index => $field)
                    <div class="row custom-field-row mb-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label">Field Name</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-tag"></i>
                                </span>
                                {{ Form::text('custom_field_names[]', $field['name'], ['class' => 'form-control', 'placeholder' => 'Field Name', 'required']) }}
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Field Value</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-edit"></i>
                                </span>
                                {{ Form::text('custom_field_values[]', $field['value'], ['class' => 'form-control', 'placeholder' => 'Field Value', 'required']) }}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-danger remove-field w-100">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="fas fa-plus-circle fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No custom fields added yet. Click "Add Custom Field" to get started.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Insurance History Section -->
    <div class="col-lg-12">
        <div class="separator separator-dashed my-10"></div>
        <h3 class="mb-5">
            <i class="fas fa-history text-primary me-2"></i>
            Insurance History
        </h3>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card bg-light-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-plus fa-2x text-primary me-3"></i>
                            <div>
                                <h6 class="mb-1">Created Date</h6>
                                <p class="mb-0">{{ $insurance->created_at->format('d M Y, h:i A') }}</p>
                                <small class="text-muted">{{ $insurance->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card bg-light-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-edit fa-2x text-warning me-3"></i>
                            <div>
                                <h6 class="mb-1">Last Updated</h6>
                                <p class="mb-0">{{ $insurance->updated_at->format('d M Y, h:i A') }}</p>
                                <small class="text-muted">{{ $insurance->updated_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Policy Duration Info -->
    <div class="col-lg-12 mt-5">
        <div class="card bg-light-info">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <i class="fas fa-clock fa-2x text-info mb-2"></i>
                        <h6>Policy Duration</h6>
                        <p class="mb-0" id="policyDuration">
                            {{ $insurance->start_date->diffInDays($insurance->end_date) }} days
                        </p>
                    </div>
                    <div class="col-md-3">
                        <i class="fas fa-calendar-day fa-2x text-info mb-2"></i>
                        <h6>Days Elapsed</h6>
                        <p class="mb-0" id="daysElapsed">
                            {{ now()->diffInDays($insurance->start_date) }} days
                        </p>
                    </div>
                    <div class="col-md-3">
                        <i class="fas fa-hourglass-half fa-2x text-info mb-2"></i>
                        <h6>Days Remaining</h6>
                        <p class="mb-0" id="daysRemaining">
                            @if($insurance->days_until_expiry > 0)
                                {{ $insurance->days_until_expiry }} days
                            @else
                                <span class="text-danger">Expired</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-3">
                        <i class="fas fa-percentage fa-2x text-info mb-2"></i>
                        <h6>Progress</h6>
                        <p class="mb-0" id="policyProgress">
                            @php
                                $totalDays = $insurance->start_date->diffInDays($insurance->end_date);
                                $elapsedDays = now()->diffInDays($insurance->start_date);
                                $progress = $totalDays > 0 ? min(100, ($elapsedDays / $totalDays) * 100) : 0;
                            @endphp
                            {{ number_format($progress, 1) }}%
                        </p>
                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar bg-info" role="progressbar" 
                                 style="width: {{ $progress }}%" 
                                 aria-valuenow="{{ $progress }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Submit Buttons -->
<div class="d-flex justify-content-end mt-10">
    <button type="button" class="btn btn-light me-3" onclick="window.history.back()">
        <i class="fas fa-times"></i> {{ __('Cancel') }}
    </button>
    <button type="submit" class="btn btn-primary" id="submitBtn">
        <i class="fas fa-save"></i> {{ __('Update Insurance') }}
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Client selection change handler
    $('#clientSelect').on('change', function() {
        const clientId = $(this).val();
        if (clientId) {
            loadClientDetails(clientId);
        } else {
            $('#clientDetailsSection').hide();
        }
    });

    // Add custom field functionality
    let customFieldCount = {{ $insurance->custom_fields ? count($insurance->custom_fields) : 0 }};
    
    $('#addCustomField').on('click', function() {
        addCustomFieldRow();
    });

    // Remove custom field
    $(document).on('click', '.remove-field', function() {
        $(this).closest('.custom-field-row').remove();
        
        // Show empty state if no fields left
        if ($('.custom-field-row').length === 0) {
            $('#customFieldsContainer').html(`
                <div class="text-center py-5">
                    <i class="fas fa-plus-circle fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No custom fields added yet. Click "Add Custom Field" to get started.</p>
                </div>
            `);
        }
    });

    // Date validation
    $('#startDate, #endDate').on('change', function() {
        validateDates();
        updatePolicyInfo();
    });

    // Form submission with validation
    $('#insuranceEditForm').on('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            return false;
        }
        
        $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
    });

    function loadClientDetails(clientId) {
        $.ajax({
            url: "{{ route('insurances.get-client-details') }}",
            type: 'GET',
            data: { client_id: clientId },
            success: function(response) {
                if (response.success) {
                    const client = response.data;
                    $('#clientDetails').html(`
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user text-info me-2"></i>
                                <div>
                                    <strong>Name:</strong> ${client.first_name} ${client.last_name}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-envelope text-info me-2"></i>
                                <div>
                                    <strong>Email:</strong> ${client.email || 'N/A'}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-phone text-info me-2"></i>
                                <div>
                                    <strong>Phone:</strong> ${client.contact || 'N/A'}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-map-marker-alt text-info me-2"></i>
                                <div>
                                    <strong>Address:</strong> ${client.address || 'N/A'}
                                </div>
                            </div>
                        </div>
                    `);
                    $('#clientDetailsSection').show();
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to load client details', 'error');
            }
        });
    }

    function addCustomFieldRow() {
        // Remove empty state if it exists
        if ($('#customFieldsContainer .text-center').length > 0) {
            $('#customFieldsContainer').empty();
        }
        
        const newRow = `
            <div class="row custom-field-row mb-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Field Name</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-tag"></i>
                        </span>
                        <input type="text" name="custom_field_names[]" class="form-control" placeholder="Field Name" required>
                    </div>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Field Value</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-edit"></i>
                        </span>
                        <input type="text" name="custom_field_values[]" class="form-control" placeholder="Field Value" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-sm btn-danger remove-field w-100">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
            </div>
        `;
        $('#customFieldsContainer').append(newRow);
        customFieldCount++;
    }

    function validateDates() {
        const startDate = new Date($('#startDate').val());
        const endDate = new Date($('#endDate').val());
        const validationDiv = $('#dateValidation');
        
        if (startDate && endDate) {
            if (endDate <= startDate) {
                validationDiv.html('<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> End date must be after start date</span>');
                return false;
            } else {
                const diffDays = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
                validationDiv.html(`<span class="text-success"><i class="fas fa-check"></i> Policy duration: ${diffDays} days</span>`);
                return true;
            }
        }
        
        validationDiv.html('');
        return true;
    }

    function updatePolicyInfo() {
        const startDate = new Date($('#startDate').val());
        const endDate = new Date($('#endDate').val());
        const now = new Date();
        
        if (startDate && endDate) {
            const totalDays = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
            const elapsedDays = Math.max(0, Math.ceil((now - startDate) / (1000 * 60 * 60 * 24)));
            const remainingDays = Math.max(0, Math.ceil((endDate - now) / (1000 * 60 * 60 * 24)));
            const progress = totalDays > 0 ? Math.min(100, (elapsedDays / totalDays) * 100) : 0;
            
            $('#policyDuration').text(totalDays + ' days');
            $('#daysElapsed').text(elapsedDays + ' days');
            $('#daysRemaining').html(remainingDays > 0 ? remainingDays + ' days' : '<span class="text-danger">Expired</span>');
            $('#policyProgress').text(progress.toFixed(1) + '%');
            
            $('.progress-bar').css('width', progress + '%').attr('aria-valuenow', progress);
        }
    }

    function validateForm() {
        let isValid = true;
        
        // Validate required fields
        $('input[required], select[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        // Validate dates
        if (!validateDates()) {
            isValid = false;
        }
        
        // Validate custom fields
        $('.custom-field-row').each(function() {
            const fieldName = $(this).find('input[name="custom_field_names[]"]').val();
            const fieldValue = $(this).find('input[name="custom_field_values[]"]').val();
            
            if (fieldName && !fieldValue) {
                $(this).find('input[name="custom_field_values[]"]').addClass('is-invalid');
                isValid = false;
            } else if (!fieldName && fieldValue) {
                $(this).find('input[name="custom_field_names[]"]').addClass('is-invalid');
                isValid = false;
            } else {
                $(this).find('input').removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            Swal.fire('Validation Error', 'Please fill in all required fields correctly.', 'error');
        }
        
        return isValid;
    }

    // Initialize Select2
    $('#clientSelect, #categorySelect').select2({
        placeholder: "Select an option",
        allowClear: true
    });

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
