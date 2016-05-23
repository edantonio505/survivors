<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use App\TopicOfTheDay;
use App\TopicOfTheDayTitle;
use App\Tag;

class PagesController extends Controller
{
    public function index()
    {	
        $tags = Tag::all();
        $titles = TopicOfTheDayTitle::all();
    	$topics = TopicOfTheDay::orderBy('created_at', 'desc')->get();
    	return view('welcome')
            ->withTopics($topics)
            ->withTitles($titles)
            ->withTags($tags);
    }


    public function userProfile($username)
    {
    	$user = User::where('name', $username)->first();
    	return view('pages.userprofile')->withUser($user);
    }
}
