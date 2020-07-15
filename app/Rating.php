<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    public function user() {
        return $this->belongsTo("App\User", "creator_id");
    }

    public function campground() {
        return $this->belongsTo("App\Campground", "campground_id");
    }
}
