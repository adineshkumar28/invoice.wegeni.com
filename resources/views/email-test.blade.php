@extends('layouts.app')

@section('title')
    {{ __('Email Testing') }}
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h1>{{ __('Email Testing & Configuration') }}</h1>
        </div>

        @include('flash::message')

         Email Configuration Check 
        <div class="card mb-5">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cog text-info me-2"></i>
                    Email Configuration
                </h3>
            </div>
            <div class="card-body">
                <button type="button" class="btn btn-info" onclick="checkEmailConfig()">
                    <i class="fas fa-check-circle"></i> Check Email Configuration
                </button>
                <div id="emailConfigResult" class="mt-3"></div>
            </div>
        </div>

         Basic Email Test 
        <div class="card mb-5">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-envelope text-primary me-2"></i>
                    Basic Email Test
                </h3>
            </div>
            <div class="card-body">
                <form id="basicEmailForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Test Email Address</label>
                                <input type="email" name="email" class="form-control" 
                                       value="{{ Auth::user()->email }}" required>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Send Basic Test Email
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

         Direct Email Test 
        <div class="card mb-5">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-envelope-open text-success me-2"></i>
                    Direct Email Test
                </h3>
            </div>
            <div class="card-body">
                <form id="directEmailForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">To Email</label>
                                <input type="email" name="to_email" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Subject</label>
                                <input type="text" name="subject" class="form-control" 
                                       value="Test Email from {{ config('app.name') }}">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-control" rows="3">Hello! This is a test email from {{ config('app.name') }}. If you receive this, email configuration is working properly.</textarea>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane"></i> Send Direct Email
                    </button>
                </form>
            </div>
        </div>

         Insurance Reminder Test 
        <div class="card mb-5">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-shield-alt text-warning me-2"></i>
                    Insurance Reminder Test
                </h3>
            </div>
            <div class="card-body">
                <form id="insuranceReminderForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Select Insurance</label>
                                <select name="insurance_id" class="form-select" required>
                                    <option value="">Choose an insurance policy</option>
                                    @foreach(\App\Models\Insurance::where('tenant_id', Auth::user()->tenant_id)->get() as $insurance)
                                        <option value="{{ $insurance->id }}">
                                            {{ $insurance->name }} - {{ $insurance->policy_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Test Email (Optional)</label>
                                <input type="email" name="test_email" class="form-control" 
                                       placeholder="Leave empty to send to actual client">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-paper-plane"></i> Send Insurance Reminder Test
                    </button>
                </form>
            </div>
        </div>

         Command Line Instructions 
        <div class="card mb-5">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-terminal text-secondary me-2"></i>
                    Command Line Testing
                </h3>
            </div>
            <div class="card-body">
                <div class="bg-dark text-light p-3 rounded">
                    <p class="text-success mb-2"><strong>1. Test Email Configuration:</strong></p>
                    <code class="text-info">php artisan tinker</code><br>
                    <code class="text-info">Mail::raw('Test', function($m) { $m->to('your-email@example.com')->subject('Test'); });</code>
                    
                    <p class="text-success mb-2 mt-3"><strong>2. Test Insurance Reminders:</strong></p>
                    <code class="text-warning">php artisan insurance:send-reminders --test</code>
                    
                    <p class="text-success mb-2 mt-3"><strong>3. Send Real Reminders:</strong></p>
                    <code class="text-danger">php artisan insurance:send-reminders</code>
                    
                    <p class="text-success mb-2 mt-3"><strong>4. Process Email Queue:</strong></p>
                    <code class="text-info">php artisan queue:work</code>
                    
                    <p class="text-success mb-2 mt-3"><strong>5. Check Laravel Logs:</strong></p>
                    <code class="text-muted">tail -f storage/logs/laravel.log</code>
                </div>
            </div>
        </div>

         Email Logs 
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history text-secondary me-2"></i>
                    Recent Email Activity
                </h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Check Laravel logs at <code>storage/logs/laravel.log</code> for detailed email activity.
                    <br>
                    You can also check your email provider's logs for delivery status.
                </div>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Basic email form
    $('#basicEmailForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');
        
        $.ajax({
            url: "{{ route('test.basic.email') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire('Success!', response.message, 'success');
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                Swal.fire('Error!', response?.message || 'Failed to send email', 'error');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Send Basic Test Email');
            }
        });
    });

    // Direct email form
    $('#directEmailForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');
        
        $.ajax({
            url: "{{ route('send.direct.email') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire('Success!', response.message, 'success');
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                Swal.fire('Error!', response?.message || 'Failed to send email', 'error');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Send Direct Email');
            }
        });
    });

    // Insurance reminder form
    $('#insuranceReminderForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');
        
        $.ajax({
            url: "{{ route('test.insurance.reminder') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire('Success!', response.message, 'success');
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                Swal.fire('Error!', response?.message || 'Failed to send email', 'error');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Send Insurance Reminder Test');
            }
        });
    });
});

function checkEmailConfig() {
    $.ajax({
        url: "{{ route('check.email.config') }}",
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                const config = response.data;
                let html = '<div class="alert alert-info"><h6>Email Configuration Status:</h6>';
                html += `<p><strong>Mail Driver:</strong> ${config.mail_driver || 'Not set'}</p>`;
                html += `<p><strong>SMTP Host:</strong> ${config.mail_host || 'Not set'}</p>`;
                html += `<p><strong>SMTP Port:</strong> ${config.mail_port || 'Not set'}</p>`;
                html += `<p><strong>Username:</strong> ${config.mail_username || 'Not set'}</p>`;
                html += `<p><strong>Encryption:</strong> ${config.mail_encryption || 'Not set'}</p>`;
                html += `<p><strong>From Address:</strong> ${config.mail_from_address || 'Not set'}</p>`;
                html += `<p><strong>From Name:</strong> ${config.mail_from_name || 'Not set'}</p>`;
                html += `<p><strong>Queue Driver:</strong> ${config.queue_driver || 'Not set'}</p>`;
                if (config.smtp_connection) {
                    html += `<p><strong>SMTP Connection:</strong> <span class="${config.smtp_connection.includes('failed') ? 'text-danger' : 'text-success'}">${config.smtp_connection}</span></p>`;
                }
                html += '</div>';
                $('#emailConfigResult').html(html);
            }
        },
        error: function(xhr) {
            $('#emailConfigResult').html('<div class="alert alert-danger">Failed to load email configuration</div>');
        }
    });
}
</script>

@endsection
