<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\QuoteItemTax
 *
 * @property int $id
 * @property int $quote_item_id
 * @property int $tax_id
 * @property float|null $tax
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\QuoteItem $quoteItem
 *
 * @method static Builder|QuoteItemTax newModelQuery()
 * @method static Builder|QuoteItemTax newQuery()
 * @method static Builder|QuoteItemTax query()
 * @method static Builder|QuoteItemTax whereCreatedAt($value)
 * @method static Builder|QuoteItemTax whereId($value)
 * @method static Builder|QuoteItemTax whereQuoteItemId($value)
 * @method static Builder|QuoteItemTax whereTax($value)
 * @method static Builder|QuoteItemTax whereTaxId($value)
 * @method static Builder|QuoteItemTax whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class QuoteItemTax extends Model
{
    use HasFactory;


    public static $rules = [
        'quote_item_id' => 'required',
        'tax_id' => 'required',
        'tax' => 'nullable',
    ];

    protected $table = 'quote_item_taxes';

    public $fillable = [
        'quote_item_id',
        'tax_id',
        'tax',
    ];

    protected $casts = [
        'quote_item_id' => 'integer',
        'tax_id' => 'integer',
        'tax' => 'double',
    ];

    public function quoteItem(): BelongsTo
    {
        return $this->belongsTo(QuoteItem::class);
    }
}
