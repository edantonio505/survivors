<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use App\TopicOfTheDay;
use App\TopicOfTheDayTitle;
use App\Tag;
use Auth;

class PagesController extends Controller
{
    public function index()
    {	
        // $link = (Auth::check() ? '<a href="/logout">Logout</a>' : '<a href="/login">Admin Login</a>');
        // return 'SpeakOut server <br />' .$link;
        $topics = TopicOfTheDay::all();

        return view('welcome', ['topics' => $topics]);
    }


    public function userProfile($username)
    {
    	$user = User::where('name', $username)->first();
    	return view('pages.userprofile')->withUser($user);
    }
}
