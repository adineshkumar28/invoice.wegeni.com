<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_group_id' => 'nullable|integer|exists:client_groups,id',
            'client_id' => 'required|integer',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'invoice_id' => 'required|string|max:255',
            'discount_type' => 'required|integer|in:0,1,2',
            'discount' => 'nullable|string',
            'amount' => 'required|string',
            'final_amount' => 'required|string',
            'insurance_id' => 'required|array|min:1',
            'insurance_id.*' => 'nullable|string',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|string',
            'price' => 'required|array|min:1', 
            'price.*' => 'required|string',
            'tax' => 'nullable|string',
            'tax_id' => 'nullable|string',
            'status' => 'required|integer|in:0,1,2,3,4',
            'template_id' => 'required|integer',
            'currency_id' => 'nullable|integer|exists:currencies,id',
            'note' => 'nullable|string',
            'term' => 'nullable|string',
            'recurring_status' => 'nullable',
            'recurring_cycle' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'Please select a client.',
            'invoice_date.required' => 'Invoice date is required.',
            'due_date.required' => 'Due date is required.',
            'due_date.after_or_equal' => 'Due date must be after or equal to invoice date.',
            'invoice_id.required' => 'Invoice number is required.',
            'amount.required' => 'Amount is required.',
            'final_amount.required' => 'Final amount is required.',
            'insurance_id.required' => 'At least one insurance item is required.',
            'quantity.required' => 'Quantities are required.',
            'quantity.*.required' => 'Quantity is required for all items.',
            'price.required' => 'Prices are required.',
            'price.*.required' => 'Price is required for all items.',
            'status.required' => 'Status is required.',
            'template_id.required' => 'Please select an invoice template.',
        ];
    }

    protected function prepareForValidation()
    {
        Log::info('Raw update request data before cleaning:', $this->all());
        
        $data = $this->all();
        
        // Clean numeric string values - handle all possible separators
        $numericFields = ['discount', 'amount', 'final_amount'];
        foreach ($numericFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = $this->sanitizeNumericString($data[$field]);
            }
        }
        
        // Clean array numeric values
        $arrayNumericFields = ['price', 'quantity'];
        foreach ($arrayNumericFields as $field) {
            if (isset($data[$field]) && is_array($data[$field])) {
                foreach ($data[$field] as $key => $value) {
                    $data[$field][$key] = $this->sanitizeNumericString($value);
                }
            }
        }
        
        // Handle JSON fields - ensure they are valid JSON strings
        if (isset($data['tax'])) {
            $data['tax'] = $this->sanitizeJsonString($data['tax']);
        }
        
        if (isset($data['tax_id'])) {
            $data['tax_id'] = $this->sanitizeJsonString($data['tax_id']);
        }
        
        // Handle boolean fields
        $data['recurring_status'] = isset($data['recurring_status']) ? 1 : 0;
        
        // Ensure arrays are properly formatted
        if (!isset($data['insurance_id']) || !is_array($data['insurance_id'])) {
            $data['insurance_id'] = [];
        }
        
        if (!isset($data['quantity']) || !is_array($data['quantity'])) {
            $data['quantity'] = [];
        }
        
        if (!isset($data['price']) || !is_array($data['price'])) {
            $data['price'] = [];
        }
        
        Log::info('Cleaned update request data:', $data);
        
        $this->replace($data);
    }
    
    private function sanitizeNumericString($value): string
    {
        if (is_null($value) || $value === '') {
            return '0';
        }
        
        // Convert to string first
        $value = (string) $value;
        
        // Remove all non-numeric characters except decimal point and minus sign
        $cleaned = preg_replace('/[^0-9.-]/', '', $value);
        
        // Handle multiple decimal points - keep only the first one
        $parts = explode('.', $cleaned);
        if (count($parts) > 2) {
            $cleaned = $parts[0] . '.' . implode('', array_slice($parts, 1));
        }
        
        // Handle multiple minus signs - keep only if at the beginning
        if (strpos($cleaned, '-') !== false) {
            $isNegative = strpos($cleaned, '-') === 0;
            $cleaned = str_replace('-', '', $cleaned);
            if ($isNegative) {
                $cleaned = '-' . $cleaned;
            }
        }
        
        // Validate the result
        if (!is_numeric($cleaned)) {
            $cleaned = '0';
        }
        
        // Format to ensure consistent decimal places
        $number = (float) $cleaned;
        return number_format($number, 2, '.', '');
    }
    
    private function sanitizeJsonString($value): string
    {
        if (empty($value) || $value === 'null' || is_null($value)) {
            return '[]';
        }
        
        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        
        if (is_string($value)) {
            // Remove any BOM or invisible characters
            $value = trim($value, "\xEF\xBB\xBF\x00..\x1F");
            
            // Test if it's valid JSON
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // Re-encode to ensure consistent formatting
                return json_encode($decoded, JSON_UNESCAPED_UNICODE);
            }
        }
        
        return '[]';
    }
}
