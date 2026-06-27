<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        return $query->with(['requester', 'assignee'])->paginate(20);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['open', 'pending', 'on_hold', 'resolved', 'closed'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'assignee_id' => ['nullable', 'exists:users,id'],
        ]);

        $ticket = Ticket::create([
            'organization_id' => auth()->user()->organization_id,
            'subject' => $validated['subject'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'requester_id' => auth()->id(),
            'assignee_id' => $validated['assignee_id'] ?? null,
        ]);

        return response()->json($ticket->load(['requester', 'assignee']), 201);
    }

    public function show(Ticket $ticket)
    {
        return $ticket->load(['requester', 'assignee', 'comments', 'activityLogs']);
    }

    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'subject' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', Rule::in(['open', 'pending', 'on_hold', 'resolved', 'closed'])],
            'priority' => ['sometimes', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'assignee_id' => ['sometimes', 'nullable', 'exists:users,id'],
        ]);

        $ticket->update($validated);

        return $ticket->load(['requester', 'assignee']);
    }
}
