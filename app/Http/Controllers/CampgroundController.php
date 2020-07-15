<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Campground;
use App\Rating;
use App\Comment;
use App\User;
use JWTAuth;

class CampgroundController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $campgrounds = Campground::with(["ratings"])->get();
        return response($campgrounds, 200);
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
            'title' => 'required',
            'description' => 'required',
            'image' => 'required',
            "address" => "required",
            'price' => 'required',
            'creator_id' => 'required'
        ]);

        $campground = new Campground;

        $campground->title = $request->title;
        $campground->description = $request->description;
        $campground->image = $request->image;
        $campground->address = $request->address;
        $campground->price = $request->price;
        $campground->creator_id = $request->creator_id;
        $campground->save();

        return response($campground, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $campground = Campground::with(['user', 'comments', "ratings",  "comments.user", "comments.likes"])->where("id", $id)->get();

        return response($campground, 200);
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
        $campground = Campground::find($id);
        $campground->title = $request->title;
        $campground->description = $request->description;
        $campground->image = $request->image;
        $campground->price = $request->price;
        $campground->save();

        return response($campground, 200);
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

        $campground = Campground::find($id);

        if($campground->creator_id !== $user->id) {
            return response()->json(["message" => 'You are not authorzied to delete this campground'], 401);
        }

        Rating::where("campground_id", $id)->delete();

        Comment::where("campground_id", $id)->delete();

        $campground->delete();

        return response("Successfully deleted campground with id of $id", 200);
    }

    public function showUser($id) {
        $campground = Campground::find($id);
        $user = $campground->user;
        return response($user, 200);
    }

    public function showComments($id) {
        $campground = Campground::find($id);
        return response($campground->comments, 200);
    }

    public function rateCampground(Request $request) {
        if($request->value > 5 || $request->value < 0.5) {
            return response()->json(["message" => 'You can only rate campgrounds with values between 0.5 - 5'], 400);
        }
        $rating = new Rating;
        $rating->campground_id = $request->campground_id;
        $rating->user_id = $request->user_id;
        $rating->value = $request->value;

        $rating->save();
        return response($rating, 201);
    }
}
