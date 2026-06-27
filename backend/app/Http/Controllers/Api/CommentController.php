<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Ticket;

class CommentController extends Controller
{
    /**
     * Create a comment on a ticket.
     *
     * POST /api/tickets/{ticket}/comments
     */
    public function store(StoreCommentRequest $request, Ticket $ticket)
    {
        $validated = $request->validated();

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
