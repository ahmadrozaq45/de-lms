<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use App\Models\DiscussionReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    /**
     * Buat thread diskusi baru.
     * POST /api/discussions
     */
    public function storeThread(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|integer|exists:courses,id',
            'title'     => 'required|string|max:255',
            'body'      => 'required|string',
        ]);

        $validated['user_id'] = Auth::id();

        return response()->json(Discussion::create($validated), 201);
    }

    /**
     * Balas thread diskusi.
     * POST /api/discussions/{id}/reply
     */
    public function reply(Request $request, int $discussionId)
    {
        $validated = $request->validate([
            'body' => 'required|string',
        ]);

        $reply = DiscussionReply::create([
            'discussion_id' => $discussionId,
            'user_id'       => Auth::id(),
            'body'          => $validated['body'],
        ]);

        return response()->json($reply, 201);
    }
}
