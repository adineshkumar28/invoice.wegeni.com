<div class="row">
    <!-- Left Column -->
    <div class="col-lg-8">
        <!-- Basic Information Card -->
        <div class="card mb-5">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="mb-0">
                        <i class="fas fa-shield-alt text-primary me-2"></i>
                        Basic Information
                    </h3>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-5">
                            {{ Form::label('name', __('Insurance Name').':', ['class' => 'form-label required mb-3']) }}
                            {{ Form::text('name', isset($insurance) ? $insurance->name : null, ['class' => 'form-control form-control-solid', 'placeholder' => __('Enter insurance name'), 'required']) }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-5">
                            {{ Form::label('policy_number', __('Policy Number').':', ['class' => 'form-label required mb-3']) }}
                            {{ Form::text('policy_number', isset($insurance) ? $insurance->policy_number : null, ['class' => 'form-control form-control-solid', 'placeholder' => __('Enter policy number'), 'required']) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Client Information Card -->
        <div class="card mb-5">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="mb-0">
                        <i class="fas fa-user text-primary me-2"></i>
                        Client Information
                    </h3>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-5">
                            {{ Form::label('client_id', __('Select Client').':', ['class' => 'form-label required mb-3']) }}
                            {{ Form::select('client_id', $clients, isset($insurance) ? $insurance->client_id : null, ['class' => 'form-select form-select-solid', 'placeholder' => __('Choose a client'), 'required', 'id' => 'clientSelect', 'data-control' => 'select2']) }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-5">
                            {{ Form::label('category_id', __('Insurance Category').':', ['class' => 'form-label required mb-3']) }}
                            {{ Form::select('category_id', $categories, isset($insurance) ? $insurance->category_id : null, ['class' => 'form-select form-select-solid', 'placeholder' => __('Choose category'), 'required', 'id' => 'categorySelect', 'data-control' => 'select2']) }}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <!-- Client Group (Info) field, informational only -->
                        <div class="mb-5">
                            {{ Form::label('client_group_info', __('Client Group (Info)').':', ['class' => 'form-label mb-3']) }}
                            {{ Form::select('client_group_info', $clientGroups ?? [], null, ['class' => 'form-select form-select-solid', 'placeholder' => __('Select to view members'), 'id' => 'clientGroupSelect', 'data-control' => 'select2', 'disabled' => true]) }}
                            <div class="form-text" id="clientGroupCountInfo">{{ __('Members:') }} <span id="clientGroupCountValue">0</span></div>
                        </div>
                    </div>
                </div>

                <!-- Client Details Display -->
                <div id="clientDetailsSection" style="display: none;">
                    <div class="separator separator-dashed my-5"></div>
                    <div class="alert alert-info d-flex align-items-center p-5">
                        <i class="fas fa-info-circle fs-2hx text-info me-4"></i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-info">Selected Client Details</h4>
                            <div class="row" id="clientDetails">
                                <!-- Client details will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Policy Details Card -->
        <div class="card mb-5">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="mb-0">
                        <i class="fas fa-file-contract text-primary me-2"></i>
                        Policy Details
                    </h3>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-5">
                            {{ Form::label('premium_amount', __('Premium Amount').':', ['class' => 'form-label required mb-3']) }}
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-dollar-sign"></i>
                                </span>
                                {{ Form::number('premium_amount', isset($insurance) ? $insurance->premium_amount : null, ['class' => 'form-control form-control-solid', 'placeholder' => __('0.00'), 'min' => '0', 'step' => '0.01', 'required']) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-5">
                            {{ Form::label('start_date', __('Start Date').':', ['class' => 'form-label required mb-3']) }}
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                                {{ Form::date('start_date', isset($insurance) ? $insurance->start_date : null, ['class' => 'form-control form-control-solid', 'required']) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-5">
                            {{ Form::label('end_date', __('End Date').':', ['class' => 'form-label required mb-3']) }}
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar-times"></i>
                                </span>
                                {{ Form::date('end_date', isset($insurance) ? $insurance->end_date : null, ['class' => 'form-control form-control-solid', 'required']) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="mb-5">
                            {{ Form::label('description', __('Description').':', ['class' => 'form-label mb-3']) }}
                            {{ Form::textarea('description', isset($insurance) ? $insurance->description : null, ['class' => 'form-control form-control-solid', 'rows' => '4', 'placeholder' => __('Enter insurance description (optional)')]) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Fields Card -->
        <div class="card mb-5">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="mb-0">
                        <i class="fas fa-cogs text-primary me-2"></i>
                        Custom Fields
                    </h3>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-success btn-sm" id="addCustomField">
                        <i class="fas fa-plus"></i> Add Field
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="customFieldsContainer">
                    @if(isset($insurance) && $insurance->custom_fields && count($insurance->custom_fields) > 0)
                        @foreach($insurance->custom_fields as $index => $field)
                            <div class="row custom-field-row mb-3 align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label">Field Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-tag"></i>
                                        </span>
                                        {{ Form::text('custom_field_names[]', $field['name'], ['class' => 'form-control', 'placeholder' => 'Enter field name', 'required']) }}
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Field Value</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-edit"></i>
                                        </span>
                                        {{ Form::text('custom_field_values[]', $field['value'], ['class' => 'form-control', 'placeholder' => 'Enter field value', 'required']) }}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm remove-field w-100">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5" id="emptyCustomFields">
                            <i class="fas fa-plus-circle fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No custom fields added yet.</p>
                            <p class="text-muted">Click "Add Field" to create custom fields for this insurance policy.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column - Summary -->
    <div class="col-lg-4">
        <div class="card position-sticky" style="top: 100px;">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="mb-0">
                        <i class="fas fa-clipboard-check text-primary me-2"></i>
                        Summary
                    </h3>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Insurance Name</h6>
                    <p class="mb-0" id="summaryName">-</p>
                </div>
                
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Policy Number</h6>
                    <p class="mb-0" id="summaryPolicy">-</p>
                </div>
                
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Selected Client</h6>
                    <p class="mb-0" id="summaryClient">-</p>
                </div>
                
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Category</h6>
                    <p class="mb-0" id="summaryCategory">-</p>
                </div>
                
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Premium Amount</h6>
                    <p class="mb-0 fw-bold text-success" id="summaryPremium">-</p>
                </div>
                
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Policy Period</h6>
                    <p class="mb-0" id="summaryPeriod">-</p>
                </div>

                <div class="separator separator-dashed my-5"></div>

                <!-- Action Buttons -->
                <div class="d-grid gap-2">
                    {{ Form::submit(__('Save Insurance'), ['class' => 'btn btn-primary', 'id' => 'saveBtn']) }}
                    <a href="{{ route('insurances.index') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left"></i> {{ __('Back to List') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Insurance form initialized');
    
    // Initialize Select2
    $('#clientSelect, #categorySelect, #clientGroupSelect').select2({
        placeholder: function() {
            return $(this).data('placeholder');
        },
        allowClear: true,
        width: '100%'
    });

    // Update summary on form changes
    updateSummary();

    // Form field listeners for summary updates
    $('input[name="name"]').on('input', updateSummary);
    $('input[name="policy_number"]').on('input', updateSummary);
    $('input[name="premium_amount"]').on('input', updateSummary);
    $('input[name="start_date"], input[name="end_date"]').on('change', updateSummary);
    
    $('#clientSelect').on('select2:select select2:clear', function() {
        const clientId = $(this).val();
        if (clientId) {
            loadClientDetails(clientId);
        } else {
            $('#clientDetailsSection').hide();
        }
        updateSummary();
    });

    $('#categorySelect').on('select2:select select2:clear', updateSummary);

    // Load existing client details if editing
    const selectedClientId = $('#clientSelect').val();
    if (selectedClientId) {
        loadClientDetails(selectedClientId);
    }

    // Custom fields functionality
    let customFieldCount = {{ isset($insurance) && $insurance->custom_fields ? count($insurance->custom_fields) : 0 }};
    
    $('#addCustomField').on('click', function() {
        addCustomFieldRow();
    });

    $(document).on('click', '.remove-field', function() {
        $(this).closest('.custom-field-row').remove();
        
        if ($('.custom-field-row').length === 0) {
            showEmptyCustomFieldsState();
        }
    });

    function updateSummary() {
        // Update insurance name
        const name = $('input[name="name"]').val() || '-';
        $('#summaryName').text(name);

        // Update policy number
        const policy = $('input[name="policy_number"]').val() || '-';
        $('#summaryPolicy').text(policy);

        // Update client
        const clientText = $('#clientSelect option:selected').text() || '-';
        $('#summaryClient').text(clientText === 'Choose a client' ? '-' : clientText);

        // Update category
        const categoryText = $('#categorySelect option:selected').text() || '-';
        $('#summaryCategory').text(categoryText === 'Choose category' ? '-' : categoryText);

        // Update premium
        const premium = $('input[name="premium_amount"]').val();
        $('#summaryPremium').text(premium ? '$' + parseFloat(premium).toFixed(2) : '-');

        // Update period
        const startDate = $('input[name="start_date"]').val();
        const endDate = $('input[name="end_date"]').val();
        if (startDate && endDate) {
            $('#summaryPeriod').text(formatDate(startDate) + ' to ' + formatDate(endDate));
        } else {
            $('#summaryPeriod').text('-');
        }
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    }

    function loadClientDetails(clientId) {
        console.log('Loading client details for:', clientId);
        
        $('#clientDetails').html(`
            <div class="col-12 text-center">
                <div class="spinner-border spinner-border-sm text-info" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span class="ms-2">Loading client details...</span>
            </div>
        `);
        $('#clientDetailsSection').show();

        $.ajax({
            url: "{{ route('insurances.get-client-details') }}",
            type: 'GET',
            data: { client_id: clientId },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success && response.data) {
                    const client = response.data;

                    // Sync Client Group (Info) field and member count
                    if (client.client_group_name) {
                        // find option by text and select (informational)
                        const $grp = $('#clientGroupSelect');
                        $grp.find('option').filter(function(){ return $(this).text() === client.client_group_name; }).prop('selected', true);
                        $grp.trigger('change.select2');
                        $('#clientGroupCountValue').text(client.client_group_count ?? 0);
                    } else {
                        $('#clientGroupSelect').val(null).trigger('change.select2');
                        $('#clientGroupCountValue').text('0');
                    }

                    $('#clientDetails').html(`
                        <div class="col-md-6 mb-2">
                            <strong><i class="fas fa-user text-info me-1"></i> Name:</strong><br>
                            <span>${client.full_name || 'N/A'}</span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong><i class="fas fa-envelope text-info me-1"></i> Email:</strong><br>
                            <span>${client.email || 'N/A'}</span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong><i class="fas fa-phone text-info me-1"></i> Phone:</strong><br>
                            <span>${client.contact || 'N/A'}</span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong><i class="fas fa-users text-info me-1"></i> Client Group:</strong><br>
                            <span>${client.client_group_name ? client.client_group_name : 'N/A'}</span>
                            ${client.client_group_name ? `<br><small class="text-muted">Members: ${client.client_group_count ?? 0}</small>` : ''}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong><i class="fas fa-building text-info me-1"></i> Company:</strong><br>
                            <span>${client.company_name || 'N/A'}</span>
                        </div>
                        ${client.address ? `
                        <div class="col-12 mb-2">
                            <strong><i class="fas fa-map-marker-alt text-info me-1"></i> Address:</strong><br>
                            <span>${client.address}</span>
                            ${client.postal_code ? `<br><small class="text-muted">Postal Code: ${client.postal_code}</small>` : ''}
                        </div>
                        ` : ''}
                    `);
                } else {
                    $('#clientDetails').html(`
                        <div class="col-12 text-center text-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span class="ms-2">Failed to load client details</span>
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading client details:', error);
                $('#clientDetails').html(`
                    <div class="col-12 text-center text-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span class="ms-2">Error loading client details</span>
                    </div>
                `);
            }
        });
    }

    function addCustomFieldRow() {
        // Hide empty state
        $('#emptyCustomFields').hide();
        
        const newRow = `
            <div class="row custom-field-row mb-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Field Name</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-tag"></i>
                        </span>
                        <input type="text" name="custom_field_names[]" class="form-control" placeholder="Enter field name" required>
                    </div>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Field Value</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-edit"></i>
                        </span>
                        <input type="text" name="custom_field_values[]" class="form-control" placeholder="Enter field value" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-field w-100">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
            </div>
        `;
        $('#customFieldsContainer').append(newRow);
        customFieldCount++;
    }

    function showEmptyCustomFieldsState() {
        $('#customFieldsContainer').html(`
            <div class="text-center py-5" id="emptyCustomFields">
                <i class="fas fa-plus-circle fa-3x text-muted mb-3"></i>
                <p class="text-muted">No custom fields added yet.</p>
                <p class="text-muted">Click "Add Field" to create custom fields for this insurance policy.</p>
            </div>
        `);
    }
});
</script>
