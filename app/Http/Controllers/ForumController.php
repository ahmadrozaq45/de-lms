<?php

namespace App\Http\Controllers;

use App\Models\{Discussion, DiscussionReply};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    public function storeThread(Request $request) {
        $validated = $request->validate(['course_id' => 'required', 'title' => 'required', 'body' => 'required']);
        $validated['user_id'] = Auth::id();
        return response()->json(Discussion::create($validated), 201);
    }

    public function reply(Request $request, $discussionId) {
        return response()->json(DiscussionReply::create(['discussion_id' => $discussionId, 'user_id' => Auth::id(), 'body' => $request->body]), 201);
    }
}
