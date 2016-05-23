<?php

namespace App\Helpers;
use Auth;
use App\User;
use App\TopicOfTheDay;
use App\Events\UserCommented;
use App\Events\UserIsInspired;
use App\Events\ConnectionCreatedPost;

class EventsNotifications {
	public function userIsInspired(TopicOfTheDay $topic, $username = null)
	{	
		if($username == null)
		{
			$username = Auth::user()->name;
		}

	    event(new UserIsInspired($username, $topic->id, $topic->user->name)); 
	}


	public function NotifyAllUsersForNewComment(TopicOfTheDay $topic, $username = null)
	{   
		$users = User::all();
	    $comments = [];

		if($username == null)
		{
			$username = Auth::user()->name;
		}

		if($username != $topic->user->name)
		{
			event(new UserCommented($username, $topic->id, $topic->user->name));
		}
	    
	    foreach($users as $user)
	    {	
	    	if($user->name != $topic->user->name)
	    	{
	    		$comments[] = $topic->comments->where('user_id', $user->id)->first();
	    	}
	    }

	    foreach($comments as $comment)
	    {
	        if($comment != null && $username != $comment->user()->name)
	        {   
	            event(new UserCommented($username, $topic->id, $comment->user()->name));
	        }
	    }
	}

	public function ConnectioPostedNewTopic(User $user, $topic_id)
	{	
		foreach($user->connections() as $receiver)
		{
			event(new ConnectionCreatedPost($user->name, $receiver->name, $topic_id));
		}
	}
}
?>