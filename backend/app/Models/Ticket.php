<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Scopes\OrganizationScope;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

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
        'subject',
        'description',
        'status',
        'priority',
        'requester_id',
        'assignee_id',
        'resolved_at',
        'first_response_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
            'first_response_at' => 'datetime',
        ];
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
