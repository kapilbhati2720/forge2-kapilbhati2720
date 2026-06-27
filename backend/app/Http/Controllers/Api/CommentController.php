<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Create a comment on a ticket.
     *
     * POST /api/tickets/{ticket}/comments
     */
    public function store(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'body' => ['required', 'string'],
            'is_internal' => ['sometimes', 'boolean'],
        ]);

        $comment = Comment::create([
            'organization_id' => $ticket->organization_id,
            'ticket_id' => $ticket->id,
            'author_id' => auth()->id(),
            'body' => $validated['body'],
            'is_internal' => $validated['is_internal'] ?? false,
        ]);

        return response()->json($comment->load('author'), 201);
    }
}
