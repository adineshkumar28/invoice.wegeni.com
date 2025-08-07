<?php

namespace App\Http\Requests;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
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
        $rules = [];

        if ($this->has('general_settings')) {
            $rules = Setting::$general_rules;
        }

        if ($this->has('invoice_settings')) {
            $rules = array_merge($rules, Setting::$invoice_rules);
        }

        return $rules;
    }

    public function messages(): array
    {
        return Setting::$messages;
    }
}
