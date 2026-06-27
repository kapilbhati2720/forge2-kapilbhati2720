<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\OrganizationScope;

class ActivityLog extends Model
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
        'ticket_id',
        'actor_id',
        'action',
        'meta',
    ];

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
