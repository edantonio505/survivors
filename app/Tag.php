<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name'];



    public function topics()
    {
    	return $this->belongsToMany('App\TopicOfTheDay', 'tag_post', 'tag_id', 'topic_id');
    }
}
