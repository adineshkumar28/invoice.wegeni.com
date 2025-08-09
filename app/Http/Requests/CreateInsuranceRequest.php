<?php

namespace App\Http\Requests;

use App\Models\Insurance;
use Illuminate\Foundation\Http\FormRequest;

class CreateInsuranceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return Insurance::$rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The insurance name field is required.',
            'policy_number.required' => 'The policy number field is required.',
            'policy_number.unique' => 'The policy number has already been taken.',
            'client_id.required' => 'Please select a client.',
            'category_id.required' => 'Please select a category.',
            'premium_amount.required' => 'The premium amount field is required.',
            'premium_amount.numeric' => 'The premium amount must be a number.',
            'start_date.required' => 'The start date field is required.',
            'end_date.required' => 'The end date field is required.',
            'end_date.after' => 'The end date must be after the start date.',
        ];
    }
}
