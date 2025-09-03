@extends('layouts.app')

@section('title')
    {{ __('Add Invoice') }}
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
                        <h1>{{ __('Add Invoice') }}</h1>
                        <a href="{{ route('invoices.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                {{ Form::open(['route' => 'invoices.store', 'id' => 'invoiceForm', 'name' => 'invoiceForm']) }}
                @include('invoices.fields')
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <!-- Hidden data for JavaScript -->
    <input type="hidden" id="insurances" value="{{ json_encode($associateInsurances) }}">
    <input type="hidden" id="taxes" value="{{ json_encode($associateTaxes) }}">
    <input type="hidden" id="currency" value="{{ getCurrencySymbol() }}">

    <div id="clientContextSection" class="d-none">
        <h2>{{ __('Client Group Context') }}</h2>
        <p>{{ __('Group Name:') }} <span id="clientGroupName">-</span></p>
        <p>{{ __('Group Count:') }} <span id="clientGroupCount">0</span></p>
        <div id="clientInsurancesList"></div>
    </div>


    <script src="{{ asset('assets/js/create-invoice-fixed.js') }}"></script>

    <script>
        (function() {
            const $ = window.jQuery;

            function fetchClientsByGroup(groupId) {
                if (!groupId) {
                    $('#client_id').html('<option value="">{{ __("Select Client") }}</option>').trigger('change');
                    resetClientContextUI();
                    applyInsuranceRestriction([]);
                    return;
                }
                $.ajax({
                    url: "{{ route('invoices.get-clients-by-group', ['groupId' => 'GROUP_ID']) }}".replace('GROUP_ID', groupId),
                    type: 'GET',
                    dataType: 'json',
                    success: function(resp) {
                        if (resp.success && resp.data && Array.isArray(resp.data.clients)) {
                            const opts = ['<option value="">{{ __("Select Client") }}</option>'].concat(
                                resp.data.clients.map(c => `<option value="${c.user_id}">${c.name}</option>`)
                            ).join('');
                            $('#client_id').html(opts).trigger('change');
                        } else {
                            $('#client_id').html('<option value="">{{ __("Select Client") }}</option>').trigger('change');
                        }
                    },
                    error: function() {
                        $('#client_id').html('<option value="">{{ __("Select Client") }}</option>').trigger('change');
                    }
                });
            }

            function fetchClientContext(userId) {
                if (!userId) {
                    resetClientContextUI();
                    applyInsuranceRestriction([]);
                    return;
                }
                $.ajax({
                    url: "{{ route('invoices.get-client-context', ['userId' => 'USER_ID']) }}".replace('USER_ID', userId),
                    type: 'GET',
                    dataType: 'json',
                    success: function(resp) {
                        if (resp.success && resp.data) {
                            updateClientContextUI({
                                group_name: resp.data.group_name,
                                group_count: resp.data.group_count,
                                insurances: resp.data.insurances || [],
                            });
                            // Restrict selectable insurances to chosen client's policies
                            applyInsuranceRestriction(resp.data.insurances || []);
                        } else {
                            resetClientContextUI();
                            applyInsuranceRestriction([]);
                        }
                    },
                    error: function() {
                        resetClientContextUI();
                        applyInsuranceRestriction([]);
                    }
                });
            }

            function resetClientContextUI() {
                $('#clientContextSection').addClass('d-none');
                $('#clientGroupName').text('-');
                $('#clientGroupCount').text('0');
                $('#clientInsurancesList').empty();
            }

            function updateClientContextUI(data) {
                $('#clientContextSection').removeClass('d-none');
                $('#clientGroupName').text(data.group_name || '-');
                $('#clientGroupCount').text(data.group_count ?? 0);

                const list = $('#clientInsurancesList').empty();
                if (Array.isArray(data.insurances) && data.insurances.length) {
                    const rows = data.insurances.map(function(ins) {
                        const period = (ins.start_date && ins.end_date) ? (ins.start_date + ' → ' + ins.end_date) : '';
                        return `<div class="small mb-1">
                            <i class="fas fa-shield-alt text-info me-1"></i>
                            <strong>${ins.name}</strong>
                            ${ins.policy_number ? ` <span class="text-muted">(#${ins.policy_number})</span>` : ''}
                            ${period ? ` <span class="text-muted">(${period})</span>` : ''}
                            ${ins.premium_amount ? ` <span class="badge bg-light text-dark ms-1">{{ getCurrencySymbol() }}${ins.premium_amount}</span>` : ''}
                        </div>`;
                    }).join('');
                    list.html(rows);
                } else {
                    list.html('<div class="text-muted">{{ __("No insurances found for this client.") }}</div>');
                }
            }

            function applyInsuranceRestriction(insList) {
                const options = ['<option value="">{{ __("Select Insurance") }}</option>']
                    .concat(insList.map(function(ins) {
                        return `<option value="${ins.id}">${ins.name}</option>`;
                    }))
                    .concat(['<option value="custom">{{ __("Enter Custom Item") }}</option>'])
                    .join('');

                $('.invoice-item-container select.insurance').each(function() {
                    const $sel = $(this);
                    const current = $sel.val();
                    $sel.html(options);

                    const allowedIds = new Set(insList.map(i => String(i.id)));
                    if (!allowedIds.has(String(current)) && current !== 'custom') {
                        $sel.val('').trigger('change');
                        const row = $sel.closest('tr');
                        row.find('.policy-number').val('').prop('readonly', true);
                        row.find('.price').val('0.00');
                    } else {
                        $sel.val(current);
                    }

                    if ($sel.hasClass('io-select2')) {
                        $sel.trigger('change.select2');
                    }
                });
            }

            // Bind changes
            $(document).on('change', '#client_group_id', function() {
                const groupId = $(this).val();
                fetchClientsByGroup(groupId);
                // reset client and restriction until a client is chosen
                $('#client_id').val('').trigger('change');
                resetClientContextUI();
                applyInsuranceRestriction([]);
            });

            $(document).on('change', '#client_id', function() {
                const userId = $(this).val();
                fetchClientContext(userId);
            });

            // On load: hydrate if defaults exist
            const initialGroupId = $('#client_group_id').val();
            if (initialGroupId) {
                fetchClientsByGroup(initialGroupId);
            }
            const initialUserId = $('#client_id').val();
            if (initialUserId) {
                fetchClientContext(initialUserId);
            }
        })();
    </script>
@endsection
