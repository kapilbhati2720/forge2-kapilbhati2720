<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TicketFlowTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsOrgUser(Organization $organization, string $role = 'agent'): User
    {
        return User::factory()->create([
            'organization_id' => $organization->id,
            'role' => $role,
            'password' => Hash::make('password'),
        ]);
    }

    public function test_ticket_list_respects_organization_isolation(): void
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();

        $userA = $this->actingAsOrgUser($orgA);
        $userB = $this->actingAsOrgUser($orgB);

        Ticket::factory()->count(3)->create([
            'organization_id' => $orgA->id,
            'requester_id' => $userA->id,
        ]);

        Ticket::factory()->count(2)->create([
            'organization_id' => $orgB->id,
            'requester_id' => $userB->id,
        ]);

        Sanctum::actingAs($userA);

        $response = $this->getJson('/api/tickets');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_authenticated_user_can_create_a_ticket(): void
    {
        $organization = Organization::factory()->create();
        $user = $this->actingAsOrgUser($organization, 'customer');

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/tickets', [
            'subject' => 'New support request',
            'description' => 'Something is broken.',
            'status' => 'open',
            'priority' => 'high',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.subject', 'New support request')
            ->assertJsonPath('data.priority', 'high');

        $this->assertDatabaseHas('tickets', [
            'subject' => 'New support request',
            'organization_id' => $organization->id,
            'requester_id' => $user->id,
        ]);
    }

    public function test_ticket_creation_requires_valid_fields(): void
    {
        $organization = Organization::factory()->create();
        $user = $this->actingAsOrgUser($organization, 'customer');

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/tickets', [
            'subject' => '',
            'status' => 'invalid_status',
            'priority' => 'invalid_priority',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['subject', 'status', 'priority']);
    }

    public function test_authenticated_user_can_create_a_comment_on_a_ticket(): void
    {
        $organization = Organization::factory()->create();
        $user = $this->actingAsOrgUser($organization, 'agent');
        $ticket = Ticket::factory()->create([
            'organization_id' => $organization->id,
            'requester_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/tickets/{$ticket->id}/comments", [
            'body' => 'This is a public reply.',
            'is_internal' => false,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.body', 'This is a public reply.')
            ->assertJsonPath('data.is_internal', false);

        $this->assertDatabaseHas('comments', [
            'ticket_id' => $ticket->id,
            'author_id' => $user->id,
            'body' => 'This is a public reply.',
        ]);
    }

    public function test_user_from_another_organization_cannot_access_a_ticket(): void
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();

        $userA = $this->actingAsOrgUser($orgA);
        $userB = $this->actingAsOrgUser($orgB);

        $ticket = Ticket::factory()->create([
            'organization_id' => $orgA->id,
            'requester_id' => $userA->id,
        ]);

        Sanctum::actingAs($userB);

        $response = $this->getJson("/api/tickets/{$ticket->id}");

        $response->assertNotFound();
    }

    public function test_user_from_another_organization_cannot_comment_on_a_ticket(): void
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();

        $userA = $this->actingAsOrgUser($orgA);
        $userB = $this->actingAsOrgUser($orgB);

        $ticket = Ticket::factory()->create([
            'organization_id' => $orgA->id,
            'requester_id' => $userA->id,
        ]);

        Sanctum::actingAs($userB);

        $response = $this->postJson("/api/tickets/{$ticket->id}/comments", [
            'body' => 'This should not work.',
        ]);

        $response->assertNotFound();

        $this->assertDatabaseMissing('comments', [
            'body' => 'This should not work.',
        ]);
    }
}
