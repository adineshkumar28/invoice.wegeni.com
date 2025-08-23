<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * App\Models\ClientGroup
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $tenant_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Client[] $clients
 * @property-read int|null $clients_count
 */
class ClientGroup extends Model
{
    use HasFactory, BelongsToTenant, Multitenantable;

    protected $table = 'client_groups';

    public $fillable = [
        'name',
        'description',
        'tenant_id',
    ];

    protected $casts = [
        'name' => 'string',
        'description' => 'string',
        'tenant_id' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|string|max:191',
        'description' => 'nullable|string',
    ];

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class, 'client_group_id', 'id');
    }
}
