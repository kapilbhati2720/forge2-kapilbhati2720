<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use App\Models\Ticket;
use App\Models\SlaPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SlaAndStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_sla_policies_crud_is_tenant_isolated()
    {
        $orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a']);
        $orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b']);

        $userA = User::create([
            'organization_id' => $orgA->id,
            'name' => 'User A',
            'email' => 'usera@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $policyA = SlaPolicy::create([
            'organization_id' => $orgA->id,
            'name' => 'Org A SLA',
            'priority' => 'high',
            'response_minutes' => 60,
            'resolution_minutes' => 120,
            'is_default' => true,
        ]);

        $policyB = SlaPolicy::create([
            'organization_id' => $orgB->id,
            'name' => 'Org B SLA',
            'priority' => 'high',
            'response_minutes' => 30,
            'resolution_minutes' => 60,
            'is_default' => true,
        ]);

        Sanctum::actingAs($userA, ['*']);

        // Assert User A sees only Org A's SLA policy
        $response = $this->getJson('/api/sla-policies');
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.id', $policyA->id);

        // Assert User A cannot read Org B's policy directly
        $response = $this->getJson("/api/sla-policies/{$policyB->id}");
        $response->assertStatus(404);
    }

    public function test_sla_priority_uniqueness_is_enforced()
    {
        $org = Organization::create(['name' => 'Acme', 'slug' => 'acme']);
        $user = User::create([
            'organization_id' => $org->id,
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        SlaPolicy::create([
            'organization_id' => $org->id,
            'name' => 'Existing SLA',
            'priority' => 'high',
            'response_minutes' => 60,
            'resolution_minutes' => 120,
        ]);

        Sanctum::actingAs($user, ['*']);

        // Post same priority high again -> must fail validation
        $response = $this->postJson('/api/sla-policies', [
            'name' => 'Duplicate Priority SLA',
            'priority' => 'high',
            'response_minutes' => 30,
            'resolution_minutes' => 60,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('priority');
    }

    public function test_dashboard_stats_calculation()
    {
        $org = Organization::create(['name' => 'Acme', 'slug' => 'acme']);
        $user = User::create([
            'organization_id' => $org->id,
            'name' => 'Agent User',
            'email' => 'agent@test.com',
            'password' => bcrypt('password'),
            'role' => 'agent',
        ]);

        // SLA Policies
        SlaPolicy::create([
            'organization_id' => $org->id,
            'name' => 'High SLA',
            'priority' => 'high',
            'response_minutes' => 60,
            'resolution_minutes' => 120,
        ]);

        // 1. SLA Met: Ticket created now, resolved in 30 mins (target is 120)
        $ticket1 = Ticket::create([
            'organization_id' => $org->id,
            'subject' => 'SLA Met Ticket',
            'priority' => 'high',
            'status' => 'resolved',
            'requester_id' => $user->id,
            'resolved_at' => now(),
        ]);
        $ticket1->created_at = now()->subMinutes(30);
        $ticket1->save();

        // 2. SLA Breached: Ticket created 4 hours ago, resolved just now (duration 240 mins > 120)
        $ticket2 = Ticket::create([
            'organization_id' => $org->id,
            'subject' => 'SLA Breached Ticket',
            'priority' => 'high',
            'status' => 'resolved',
            'requester_id' => $user->id,
            'resolved_at' => now(),
        ]);
        $ticket2->created_at = now()->subHours(4);
        $ticket2->save();

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson('/api/stats');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'counts_by_status',
                'counts_by_priority',
                'average_resolution_minutes',
                'sla' => [
                    'breached_count',
                    'total_evaluated',
                    'breach_rate_percent',
                ]
            ]
        ]);

        $response->assertJsonPath('data.counts_by_status.resolved', 2);
        $response->assertJsonPath('data.sla.breached_count', 1);
        $response->assertJsonPath('data.sla.total_evaluated', 2);
        $response->assertJsonPath('data.sla.breach_rate_percent', 50);
    }
}
