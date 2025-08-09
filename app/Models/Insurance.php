<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Stancl\Tenancy\Database\TenantScope;
use Carbon\Carbon;

class Insurance extends Model
{
    use HasFactory, Notifiable, BelongsToTenant, Multitenantable;

    protected $table = 'insurances';

    protected $fillable = [
        'name',
        'policy_number',
        'client_id',
        'category_id',
        'premium_amount',
        'start_date',
        'end_date',
        'description',
        'custom_fields',
        'tenant_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'premium_amount' => 'decimal:2',
        'custom_fields' => 'array',
        'client_id' => 'integer',
        'category_id' => 'integer',
    ];

    protected $appends = ['is_expired', 'days_until_expiry', 'client_name'];

    public static $rules = [
        'name' => 'required|max:191',
        'policy_number' => 'required|unique:insurances,policy_number',
        'client_id' => 'required|exists:clients,id',
        'category_id' => 'required|exists:categories,id',
        'premium_amount' => 'required|numeric|min:0',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id')
                   ->withoutGlobalScope(new TenantScope())
                   ->with('user'); // Include user relationship
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->end_date < Carbon::now();
    }

    public function getDaysUntilExpiryAttribute(): int
    {
        return Carbon::now()->diffInDays($this->end_date, false);
    }

    public function getClientNameAttribute(): string
    {
        if ($this->client && $this->client->user) {
            return trim(($this->client->user->first_name ?? '') . ' ' . ($this->client->user->last_name ?? ''));
        }
        return 'N/A';
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('end_date', '>=', Carbon::now())
                    ->where('end_date', '<=', Carbon::now()->addDays($days));
    }

    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', Carbon::now());
    }
}
