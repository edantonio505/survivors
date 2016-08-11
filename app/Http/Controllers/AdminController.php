<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TopicOfTheDayTitle;
use App\Http\Requests;
use Auth;

class AdminController extends Controller
{
    public function dashboard()
    {   
        if(Auth::user()->email != 'edantonio505@gmail.com')
        {
            return redirect('/');
        }
    	$topics = TopicOfTheDayTitle::all();
    	return view('admin.dashboard')->withTopics($topics);
    }

    public function createTopicTitle(Request $request)
    {	
        if(Auth::user()->email != 'edantonio505@gmail.com')
        {
            return redirect('/');
        }
    	$topicTitle = New TopicOfTheDayTitle();
    	$topicTitle->topic_title = $request->input('topic_title');
    	$topicTitle->save();
    	return redirect()->back();
    }
}