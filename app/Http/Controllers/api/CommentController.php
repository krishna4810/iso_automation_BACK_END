<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function addComment(Request $request): void
    {
        $id = $request->input('function_id');
        $comment = new Comment([
            'user_id' => $request->input('user_id'),
            'function_id' => $id,
            'comment' => $request->input('comment'),
        ]);
        $comment->save();
    }

    public function getComment(Request $request) {
        $id = $request->input('id');
        $comment = DB::table('comments')
            ->where('comments.function_id', $id)
            ->orderBy('comments.created_at', 'desc')
            ->get();
        return response()->json($comment);
    }
}
