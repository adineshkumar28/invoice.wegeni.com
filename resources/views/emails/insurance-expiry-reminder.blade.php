<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insurance Expiry Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
            border: 1px solid #e9ecef;
        }
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            font-weight: bold;
        }
        .alert-danger {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .alert-warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        .alert-info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        .insurance-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #666;
        }
        .value {
            color: #333;
        }
        .cta-button {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🛡️ Insurance Policy Reminder</h1>
        <p>{{ config('app.name') }}</p>
    </div>

    <div class="content">
        @if($reminderType === 'expired')
            <div class="alert alert-danger">
                ⚠️ <strong>POLICY EXPIRED!</strong> Your insurance policy has expired and is no longer active.
            </div>
        @elseif($reminderType === 'final')
            <div class="alert alert-danger">
                🚨 <strong>FINAL NOTICE!</strong> Your insurance policy expires tomorrow!
            </div>
        @elseif($reminderType === 'urgent' || $daysUntilExpiry <= 7)
            <div class="alert alert-warning">
                ⏰ <strong>URGENT REMINDER!</strong> Your insurance policy expires in {{ $daysUntilExpiry }} {{ $daysUntilExpiry == 1 ? 'day' : 'days' }}.
            </div>
        @else
            <div class="alert alert-info">
                📅 <strong>Friendly Reminder:</strong> Your insurance policy will expire in {{ $daysUntilExpiry }} days.
            </div>
        @endif

        <p>Hello,</p>
        
        <p>
            @if($reminderType === 'expired')
                Your insurance policy has expired. Please contact us immediately to discuss renewal options to avoid any gaps in coverage.
            @elseif($reminderType === 'final')
                This is your final reminder that your insurance policy expires tomorrow. Please take immediate action to renew your policy.
            @else
                We wanted to remind you that your insurance policy is approaching its expiration date. To ensure continuous coverage, please review and renew your policy before it expires.
            @endif
        </p>

        <div class="insurance-details">
            <h3>📋 Policy Details</h3>
            
            <div class="detail-row">
                <span class="label">Insurance Name:</span>
                <span class="value">{{ $insurance->name }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Policy Number:</span>
                <span class="value">{{ $insurance->policy_number }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Premium Amount:</span>
                <span class="value">{{ getCurrencyAmount($insurance->premium_amount, true) }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Policy Start Date:</span>
                <span class="value">{{ $insurance->start_date->format('d M Y') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Policy End Date:</span>
                <span class="value" style="color: {{ $reminderType === 'expired' ? '#dc3545' : ($daysUntilExpiry <= 7 ? '#fd7e14' : '#28a745') }}; font-weight: bold;">
                    {{ $insurance->end_date->format('d M Y') }}
                </span>
            </div>
            
            @if($insurance->category)
            <div class="detail-row">
                <span class="label">Category:</span>
                <span class="value">{{ $insurance->category->name }}</span>
            </div>
            @endif

            @if($daysUntilExpiry >= 0)
            <div class="detail-row">
                <span class="label">Days Until Expiry:</span>
                <span class="value" style="color: {{ $daysUntilExpiry <= 7 ? '#dc3545' : ($daysUntilExpiry <= 30 ? '#fd7e14' : '#28a745') }}; font-weight: bold;">
                    {{ $daysUntilExpiry }} {{ $daysUntilExpiry == 1 ? 'day' : 'days' }}
                </span>
            </div>
            @endif
        </div>

        @if($insurance->description)
        <div style="background: #fff; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #007bff;">
            <strong>Policy Description:</strong>
            <p style="margin: 10px 0 0 0;">{{ $insurance->description }}</p>
        </div>
        @endif

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('insurances.show', $insurance->id) }}" class="cta-button">
                📄 View Full Policy Details
            </a>
        </div>

        <div style="background: #e9ecef; padding: 20px; border-radius: 5px; margin: 20px 0;">
            <h4 style="margin-top: 0; color: #495057;">📞 Need Help?</h4>
            <p style="margin-bottom: 0;">
                If you have any questions about your policy renewal or need assistance, please don't hesitate to contact us. 
                Our team is here to help ensure you maintain continuous coverage.
            </p>
        </div>

        @if($reminderType === 'expired')
        <div style="background: #f8d7da; padding: 20px; border-radius: 5px; margin: 20px 0; border: 1px solid #f5c6cb;">
            <h4 style="margin-top: 0; color: #721c24;">⚠️ Important Notice</h4>
            <p style="margin-bottom: 0; color: #721c24;">
                <strong>Your policy has expired and you are no longer covered.</strong> 
                Please contact us immediately to discuss your renewal options and restore your coverage.
            </p>
        </div>
        @endif

        <p>Thank you for choosing {{ config('app.name') }} for your insurance needs.</p>
        
        <p>Best regards,<br>
        <strong>{{ config('app.name') }} Team</strong></p>
    </div>

    <div class="footer">
        <p>This is an automated reminder. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        @if(config('app.url'))
        <p><a href="{{ config('app.url') }}" style="color: #007bff;">Visit our website</a></p>
        @endif
    </div>
</body>
</html>
