<?php

namespace App\Http\Requests;

use App\Models\Insurance;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInsuranceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = Insurance::$rules;
        $rules['policy_number'] = [
            'required',
            Rule::unique('insurances')->ignore($this->route('insurance')->id)
        ];
        
        return $rules;
    }
}
