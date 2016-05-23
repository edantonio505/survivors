<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\TopicOfTheDayTitle;

class TopicOfTheDay extends Model
{	
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 
        'body',
        'slug',
        'topic_title_id',
        'video'
    ];

    public function tags()
    {
        return $this->belongsToMany('App\Tag', 'tag_post', 'topic_id', 'tag_id');
    }

    public function user()
    {
    	return $this->belongsTo('App\User');
    }
    
    public function photos()
    {
        return $this->hasMany('App\Photo');
    }

    public function inspired()
    {
        return $this->morphMany('App\Inspiration', 'inspirational');
    }

    public function summary()
    {
        return substr($this->body, 0, 100);
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }


    public function getTitle()
    {   
        $title = TopicOfTheDayTitle::where('id', $this->topic_title_id)->first();
        return $title->topic_title;
    }



    public function commentsCount()
    {
        $comments = $this->comments->count();

        if($comments == 0)
        {
            return "No comments";
        }

        if($comments == 1){
            return "1 comment";
        }

        if($comments > 1){
            return $comments." comments";
        }
    }

    public function inspiredCount()
    {
        if($this->inspired->count() < 1)
        {
            return "no inspired people";
        }

        if($this->inspired->count() == 1)
        {
            return "inspired 1 person";
        }

        if($this->inspired->count() > 1)
        {
            return "inspired ".$this->inspired->count()." people";
        }
    }


    public function checkIfHasPhotos()
    {
        return (bool) $this->photos()->count();
    }


    public function checkIfHasVideo()
    {
        if($this->video != '')
        {
            return true;
        }
    }

}
