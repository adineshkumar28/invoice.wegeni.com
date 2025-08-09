<div class="row gx-10 mb-5">
    <!-- Basic Insurance Information -->
    <div class="col-lg-6">
        <div class="mb-5">
            {{ Form::label('name', __('Insurance Name').':', ['class' => 'form-label required mb-3']) }}
            {{ Form::text('name', isset($insurance) ? $insurance->name : null, ['class' => 'form-control form-control-solid', 'placeholder' => __('Insurance Name'), 'required']) }}
        </div>
    </div>

    <div class="col-lg-6">
        <div class="mb-5">
            {{ Form::label('policy_number', __('Policy Number').':', ['class' => 'form-label required mb-3']) }}
            {{ Form::text('policy_number', isset($insurance) ? $insurance->policy_number : null, ['class' => 'form-control form-control-solid', 'placeholder' => __('Policy Number'), 'required']) }}
        </div>
    </div>

    <!-- Client Selection with Details -->
    <div class="col-lg-6">
        <div class="mb-5">
            {{ Form::label('client_id', __('Client').':', ['class' => 'form-label required mb-3']) }}
            {{ Form::select('client_id', $clients, isset($insurance) ? $insurance->client_id : null, ['class' => 'form-select form-select-solid', 'placeholder' => __('Select Client'), 'required', 'id' => 'clientSelect', 'data-control' => 'select2']) }}
        </div>
    </div>

    <div class="col-lg-6">
        <div class="mb-5">
            {{ Form::label('category_id', __('Insurance Category').':', ['class' => 'form-label required mb-3']) }}
            {{ Form::select('category_id', $categories, isset($insurance) ? $insurance->category_id : null, ['class' => 'form-select form-select-solid', 'placeholder' => __('Select Category'), 'required', 'id' => 'categorySelect', 'data-control' => 'select2']) }}
        </div>
    </div>

    <!-- Client Details Display -->
    <div class="col-lg-12" id="clientDetailsSection" style="display: none;">
        <div class="card border-primary mb-5">
            <div class="card-header bg-light-primary">
                <div class="card-title">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-info-circle me-2"></i>
                        Client Details
                    </h5>
                </div>
            </div>
            <div class="card-body">
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
            {{ Form::number('premium_amount', isset($insurance) ? $insurance->premium_amount : null, ['class' => 'form-control form-control-solid', 'placeholder' => __('Premium Amount'), 'min' => '0', 'step' => '0.01', 'required']) }}
        </div>
    </div>

    <div class="col-lg-4">
        <div class="mb-5">
            {{ Form::label('start_date', __('Start Date').':', ['class' => 'form-label required mb-3']) }}
            {{ Form::date('start_date', isset($insurance) ? $insurance->start_date : null, ['class' => 'form-control form-control-solid', 'required']) }}
        </div>
    </div>

    <div class="col-lg-4">
        <div class="mb-5">
            {{ Form::label('end_date', __('End Date').':', ['class' => 'form-label required mb-3']) }}
            {{ Form::date('end_date', isset($insurance) ? $insurance->end_date : null, ['class' => 'form-control form-control-solid', 'required']) }}
        </div>
    </div>

    <!-- Description -->
    <div class="col-lg-12">
        <div class="mb-5">
            {{ Form::label('description', __('Description').':', ['class' => 'form-label mb-3']) }}
            {{ Form::textarea('description', isset($insurance) ? $insurance->description : null, ['class' => 'form-control form-control-solid', 'rows' => '4', 'placeholder' => __('Insurance Description')]) }}
        </div>
    </div>

    <!-- Custom Fields Section -->
    <div class="col-lg-12">
        <div class="separator separator-dashed my-10"></div>
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h3>Custom Fields</h3>
            <button type="button" class="btn btn-success" id="addCustomField">
                <i class="fas fa-plus"></i> Add Custom Field
            </button>
        </div>
        
        <div id="customFieldsContainer">
            @if(isset($insurance) && $insurance->custom_fields)
                @foreach($insurance->custom_fields as $index => $field)
                    <div class="row custom-field-row mb-3">
                        <div class="col-md-5">
                            {{ Form::text('custom_field_names[]', $field['name'], ['class' => 'form-control', 'placeholder' => 'Field Name']) }}
                        </div>
                        <div class="col-md-5">
                            {{ Form::text('custom_field_values[]', $field['value'], ['class' => 'form-control', 'placeholder' => 'Field Value']) }}
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-danger remove-field">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<!-- Submit Buttons -->
<div class="d-flex justify-content-end">
    {{ Form::submit(__('Save'), ['class' => 'btn btn-primary me-2']) }}
    <a href="{{ route('insurances.index') }}" type="reset"
       class="btn btn-secondary btn-active-light-primary">{{ __('Cancel') }}</a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2 first
    $('#clientSelect, #categorySelect').select2({
        placeholder: "Select an option",
        allowClear: true
    });

    // Client selection change handler
    $('#clientSelect').on('select2:select', function(e) {
        const clientId = e.params.data.id;
        if (clientId) {
            loadClientDetails(clientId);
        }
    });

    $('#clientSelect').on('select2:clear', function() {
        $('#clientDetailsSection').hide();
    });

    // Load client details on page load if client is already selected
    const selectedClientId = $('#clientSelect').val();
    if (selectedClientId) {
        loadClientDetails(selectedClientId);
    }

    // Add custom field functionality
    let customFieldCount = {{ isset($insurance) && $insurance->custom_fields ? count($insurance->custom_fields) : 0 }};
    
    $('#addCustomField').on('click', function() {
        addCustomFieldRow();
    });

    // Remove custom field
    $(document).on('click', '.remove-field', function() {
        $(this).closest('.custom-field-row').remove();
    });

    function loadClientDetails(clientId) {
        // Show loading state
        $('#clientDetails').html(`
            <div class="col-12 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading client details...</p>
            </div>
        `);
        $('#clientDetailsSection').show();

        $.ajax({
            url: "{{ route('insurances.get-client-details') }}",
            type: 'GET',
            data: { client_id: clientId },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Response:', response); // Debug log
                if (response.success && response.data) {
                    const client = response.data;
                    $('#clientDetails').html(`
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user text-primary me-2"></i>
                                <div>
                                    <strong>Name:</strong> ${client.first_name || ''} ${client.last_name || ''}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                <div>
                                    <strong>Email:</strong> ${client.email || 'N/A'}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-phone text-primary me-2"></i>
                                <div>
                                    <strong>Phone:</strong> ${client.contact || 'N/A'}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-building text-primary me-2"></i>
                                <div>
                                    <strong>Company:</strong> ${client.company_name || 'N/A'}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-map-marker-alt text-primary me-2 mt-1"></i>
                                <div>
                                    <strong>Address:</strong> ${client.address || 'N/A'}
                                </div>
                            </div>
                        </div>
                    `);
                    $('#clientDetailsSection').show();
                } else {
                    $('#clientDetails').html(`
                        <div class="col-12 text-center text-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p>Failed to load client details</p>
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText); // Debug log
                $('#clientDetails').html(`
                    <div class="col-12 text-center text-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Error loading client details: ${error}</p>
                    </div>
                `);
            }
        });
    }

    function addCustomFieldRow() {
        const newRow = `
            <div class="row custom-field-row mb-3">
                <div class="col-md-5">
                    <input type="text" name="custom_field_names[]" class="form-control" placeholder="Field Name" required>
                </div>
                <div class="col-md-5">
                    <input type="text" name="custom_field_values[]" class="form-control" placeholder="Field Value" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-sm btn-danger remove-field">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        $('#customFieldsContainer').append(newRow);
        customFieldCount++;
    }
});
</script>
