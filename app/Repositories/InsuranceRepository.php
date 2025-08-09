<?php

namespace App\Repositories;

use App\Models\Insurance;
use Illuminate\Support\Facades\Auth;

class InsuranceRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'policy_number',
        'client_id',
        'category_id',
        'premium_amount'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Insurance::class;
    }

    public function store($input): Insurance
    {
        $input['tenant_id'] = Auth::user()->tenant_id;
        
        // Handle custom fields
        if (isset($input['custom_field_names']) && isset($input['custom_field_values'])) {
            $customFields = [];
            $fieldNames = $input['custom_field_names'];
            $fieldValues = $input['custom_field_values'];
            
            for ($i = 0; $i < count($fieldNames); $i++) {
                if (!empty($fieldNames[$i]) && !empty($fieldValues[$i])) {
                    $customFields[] = [
                        'name' => $fieldNames[$i],
                        'value' => $fieldValues[$i]
                    ];
                }
            }
            
            $input['custom_fields'] = $customFields;
        }

        unset($input['custom_field_names'], $input['custom_field_values']);
        
        return Insurance::create($input);
    }

    public function updateInsurance($input, $insuranceId): Insurance
    {
        $insurance = Insurance::findOrFail($insuranceId);
        
        // Handle custom fields
        if (isset($input['custom_field_names']) && isset($input['custom_field_values'])) {
            $customFields = [];
            $fieldNames = $input['custom_field_names'];
            $fieldValues = $input['custom_field_values'];
            
            for ($i = 0; $i < count($fieldNames); $i++) {
                if (!empty($fieldNames[$i]) && !empty($fieldValues[$i])) {
                    $customFields[] = [
                        'name' => $fieldNames[$i],
                        'value' => $fieldValues[$i]
                    ];
                }
            }
            
            $input['custom_fields'] = $customFields;
        }

        unset($input['custom_field_names'], $input['custom_field_values']);
        
        $insurance->update($input);
        return $insurance;
    }
}
