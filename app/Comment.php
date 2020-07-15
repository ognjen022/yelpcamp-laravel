<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public function campground() {
        return $this->belongsTo("App\Campground", "campground_id");
    }

    public function user() {
        return $this->belongsTo("App\User", "creator_id");
    }

    public function likes() {
        return $this->hasMany("App\Like");
    }
}
