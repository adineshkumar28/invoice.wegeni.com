<?php

namespace App\Http\Requests;

use App\Models\SuperAdminSetting;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSuperAdminSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = SuperAdminSetting::$rules;

        if (request()->stripe_enabled == 'on') {
            $rules['stripe_key'] = 'required';
            $rules['stripe_secret'] = 'required';
        }
        if (request()->paypal_enabled == 'on') {
            $rules['paypal_client_id'] = 'required';
            $rules['paypal_secret'] = 'required';
        }
        if (request()->razorpay_enabled == 'on') {
            $rules['razorpay_key'] = 'required';
            $rules['razorpay_secret'] = 'required';
        }
        if (request()->paystack_enabled == 'on') {
            $rules['paystack_key'] = 'required';
            $rules['paystack_secret'] = 'required';
        }

        if (request()->enable_google_recaptcha == 'on') {
            $rules['google_captcha_key'] = 'required';
            $rules['google_captcha_secret'] = 'required';
        }

        return $rules;
    }
}
