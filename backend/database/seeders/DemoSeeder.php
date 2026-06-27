<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Organization;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::create([
            'name' => 'Acme Corp',
            'slug' => 'acme-corp',
            'domain' => 'acme.test',
            'plan' => 'pro',
            'settings' => null,
        ]);

        $admin = User::create([
            'name' => 'Acme Admin',
            'email' => 'admin@acme.test',
            'password' => Hash::make('password'),
            'organization_id' => $organization->id,
            'role' => 'admin',
        ]);

        $agents = collect([
            ['name' => 'Agent One', 'email' => 'agent1@acme.test'],
            ['name' => 'Agent Two', 'email' => 'agent2@acme.test'],
        ])->map(fn ($data) => User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make('password'),
            'organization_id' => $organization->id,
            'role' => 'agent',
        ]));

        $customers = collect([
            ['name' => 'Customer One', 'email' => 'customer1@acme.test'],
            ['name' => 'Customer Two', 'email' => 'customer2@acme.test'],
        ])->map(fn ($data) => User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make('password'),
            'organization_id' => $organization->id,
            'role' => 'customer',
        ]));

        $statuses = ['open', 'pending', 'on_hold', 'resolved', 'closed'];
        $priorities = ['low', 'medium', 'high', 'urgent'];

        $tickets = collect(range(1, 12))->map(function ($index) use ($organization, $customers, $agents, $statuses, $priorities) {
            return Ticket::create([
                'organization_id' => $organization->id,
                'subject' => "Ticket #{$index}: " . fake()->sentence(),
                'description' => fake()->paragraph(),
                'status' => $statuses[$index % count($statuses)],
                'priority' => $priorities[$index % count($priorities)],
                'requester_id' => $customers->random()->id,
                'assignee_id' => $index % 3 === 0 ? null : $agents->random()->id,
                'resolved_at' => null,
                'first_response_at' => null,
            ]);
        });

        $ticketOne = $tickets->first();

        Comment::create([
            'organization_id' => $ticketOne->organization_id,
            'ticket_id' => $ticketOne->id,
            'author_id' => $agents->first()->id,
            'body' => 'Initial public reply on ticket one.',
            'is_internal' => false,
            'attachments' => null,
        ]);

        Comment::create([
            'organization_id' => $ticketOne->organization_id,
            'ticket_id' => $ticketOne->id,
            'author_id' => $agents->last()->id,
            'body' => 'Internal note for agents only.',
            'is_internal' => true,
            'attachments' => null,
        ]);

        Comment::create([
            'organization_id' => $ticketOne->organization_id,
            'ticket_id' => $ticketOne->id,
            'author_id' => $customers->first()->id,
            'body' => 'Follow-up from the customer.',
            'is_internal' => false,
            'attachments' => null,
        ]);
    }
}
