<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Comment;
use App\Like;
use JWTAuth;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $comments = Comment::with("user")->with("campground")->get();
        return response($comments, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'content' => 'required',
            'campground_id' => 'required',
            'creator_id' => 'required'
        ]);

        $comment = new Comment;

        $comment->content = $request->content;
        $comment->campground_id = $request->campground_id;
        $comment->creator_id = $request->creator_id;

        $comment->save();

        return response($comment, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $comment = Comment::with("user")->with("campground")->where("id", $id)->get();
        return response($comment, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['user_not_found'], 404);
        }

        $comment = Comment::find($id);

        if($comment->creator_id !== $user->id) {
            return response()->json(["message" => 'You are not authorzied to update this comment'], 401);
        }

        $comment->content = $request->content;
        $comment->save();

        return response($comment, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['user_not_found'], 404);
        }

        $comment = Comment::find($id);

        if($comment->creator_id !== $user->id) {
            return response()->json(["message" => 'You are not authorzied to delete this comment'], 401);
        }

        Like::where("comment_id", $comment->id)->delete();

        $comment->delete();

        return response(["message" => "Successfully deleted comment with id of $id"], 200);
    }

    public function showUser($id) {
        $comment = Comment::find($id);
        return response($comment->user, 200);
    }

    public function showCampground($id) {
        $comment = Comment::find($id);
        return response($comment->campground, 200);
    }

    public function likeComment(Request $request, $id) {
        $comment = Comment::find($id);
        $likeExists = Like::where("comment_id", $comment->id)->where("user_id", $request->user_id)->get();

        if(count($likeExists) > 0) {
            return response(["message" => "You already liked this comment"], 400);
        }

        $like = new Like;
        $like->comment_id = $id;
        $like->user_id = $request->user_id;
        $like->save();

        return response($like, 201);
    }

    public function unlikeComment(Request $request, $id) {
        $comment = Comment::find($id);

        $likes = Like::where("user_id", $request->user_id)->where("comment_id", $comment->id)->get();
        $likes[0]->delete();

        return response($likes[0], 200);
    }

}
