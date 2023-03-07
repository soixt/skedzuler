<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    public $timestamps = false;

    protected $fillable = ['description', 'event_date', 'user_id'];

    public function user () {
        return $this->belongsTo(User::class);
    }
}
