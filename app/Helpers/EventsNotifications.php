<?php

namespace App\Helpers;
use Auth;
use App\User;
use App\TopicOfTheDay;
use App\Events\UserCommented;
use App\Events\UserIsInspired;
use App\Events\ConnectionCreatedPost;
use App\ReportedUsers;

class EventsNotifications {
	public function userIsInspired(TopicOfTheDay $topic, $username = null)
	{	
		$blocked = false;
		if($username == null){$username = Auth::user()->name;}
		$r = ReportedUsers::where('user_id', User::where('name', $username)->first()->id)->first();
		if(isset($r) && $r->blocked != 0){$blocked = true;}

		if(!$blocked)
		{
		    event(new UserIsInspired($username, $topic->id, $topic->user->name));
		    if($username != $topic->user->name)
			{
				$this->createEventLog('user_inspired', $topic->user, $username, $topic->id); 
			}
		}
	}	


	public function NotifyAllUsersForNewComment(TopicOfTheDay $topic, $username = null)
	{   
		$users = User::all();
	    $comments = [];
	    $blocked = false;
		if($username == null){$username = Auth::user()->name;}
		$r = ReportedUsers::where('user_id', User::where('name', $username)->first()->id)->first();
		if(isset($r) && $r->blocked != 0){$blocked = true;}

		if(!$blocked)
		{
			if($username != $topic->user->name)
			{	
				$this->createEventLog('new_comment', $topic->user, $username, $topic->id);
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
		        	$this->createEventLog('new_comment', $comment->user(),  $username, $topic->id);
		            event(new UserCommented($username, $topic->id, $comment->user()->name));
		        }
		    }
		}
	}

	public function ConnectioPostedNewTopic(User $user, $topic_id)
	{	
		$r = ReportedUsers::where('user_id', $user->id)->first();

		if(!$r || (isset($r) && $r->blocked == 0)){
			foreach($user->connections() as $receiver)
			{
				$this->createEventLog('connections_newpost', $receiver, $user->name, $topic_id);
				event(new ConnectionCreatedPost($user->name, $receiver->name, $topic_id));
			}
		}
	}

	public function createEventLog($type, User $receptor, $emmiter, $topic_id = null)
	{	
		if($type == 'new_comment'){$emmiterType = 'userCommenter';}
		if($type == 'user_inspired'){$emmiterType = 'inspiredUser';}
		if($type == 'new_connection'){$emmiterType = 'newConnection';}
		if($type == 'user_accepted_connection'){$emmiterType = 'AcceptingUser';}
		if($type == 'connections_newpost'){$emmiterType = 'connectionPosting';}

		$receptor->EventLogs()->create([
			'type' => $type,
			'topic_id' => $topic_id,
			$emmiterType => $emmiter
		]);
	}
}
?>