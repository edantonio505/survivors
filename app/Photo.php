<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{	
	protected $fillable = ['name','path', 'thumbnail_path'];


    public function topic()
    {
    	return $this->belongsTo('App\TopicOfTheDay');
    }
}
