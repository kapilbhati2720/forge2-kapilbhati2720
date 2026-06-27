<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\OrganizationScope;

class SlaPolicy extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizationScope);
    }

    public static function withoutTenantScope(): Builder
    {
        return static::withoutGlobalScope(OrganizationScope::class);
    }

    protected $fillable = [
        'organization_id',
        'name',
        'priority',
        'response_minutes',
        'resolution_minutes',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'response_minutes' => 'integer',
            'resolution_minutes' => 'integer',
        ];
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
