<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceItem extends Model
{
    use HasFactory;

    public static $rules = [
        'insurance_id' => 'required_without:product_id',
        'product_id' => 'required_without:insurance_id',
        'quantity' => 'required',
        'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
    ];

    protected $table = 'invoice_items';

    public $fillable = [
        'invoice_id',
        'product_id',
        'product_name',
        'insurance_id',
        'insurance_name',
        'policy_number',
        'premium_amount',
        'policy_start_date',
        'policy_end_date',
        'quantity',
        'price',
        'total',
    ];

    protected $casts = [
        'invoice_id' => 'integer',
        'product_id' => 'integer',
        'insurance_id' => 'integer',
        'product_name' => 'string',
        'insurance_name' => 'string',
        'policy_number' => 'string',
        'premium_amount' => 'decimal:2',
        'policy_start_date' => 'date',
        'policy_end_date' => 'date',
        'quantity' => 'double',
        'price' => 'double',
        'total' => 'double',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function insurance(): BelongsTo
    {
        return $this->belongsTo(Insurance::class, 'insurance_id', 'id');
    }

    public function invoiceItemTax(): HasMany
    {
        return $this->hasMany(InvoiceItemTax::class);
    }

    // Get display name for the item (insurance or product)
    public function getDisplayNameAttribute(): string
    {
        if ($this->insurance_id && $this->insurance) {
            return $this->insurance->name . ' (' . $this->insurance->policy_number . ')';
        } elseif ($this->insurance_name) {
            return $this->insurance_name . ($this->policy_number ? ' (' . $this->policy_number . ')' : '');
        } elseif ($this->product_id && $this->product) {
            return $this->product->name;
        } else {
            return $this->product_name ?? 'N/A';
        }
    }
}
