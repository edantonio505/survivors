<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TopicOfTheDayTitle;
use App\Http\Requests;

class AdminController extends Controller
{
    public function dashboard()
    {
    	$topics = TopicOfTheDayTitle::all();
    	return view('admin.dashboard')->withTopics($topics);
    }

    public function createTopicTitle(Request $request)
    {	
    	$topicTitle = New TopicOfTheDayTitle();
    	$topicTitle->topic_title = $request->input('topic_title');
    	$topicTitle->save();
    	return redirect()->back();
    }
}