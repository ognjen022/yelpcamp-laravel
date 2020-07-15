<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campground extends Model
{
    public function user() {
        return $this->belongsTo("App\User", "creator_id");
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function ratings()
    {
        return $this->hasMany('App\Rating');
    }
}
