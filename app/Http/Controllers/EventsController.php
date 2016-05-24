<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;

class EventsController extends Controller
{
    public function logsViewed($username)
    {	
    	$user = User::where('name', $username)->first();
    	$events = $user->EventLogs;

    	if($events->count() > 0)
    	{
    		foreach($events as $event)
    		{
    			$event->delete();
    		}
    	}

    	return 'logs deleted';
    }
}
