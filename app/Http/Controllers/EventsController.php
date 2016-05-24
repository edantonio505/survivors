<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;

class EventsController extends Controller
{
    public function logsViewed($username)
    {	
    	$events = User::where('name', $username)->first()->EventLogs;
    	
    	if($events->count() > 0)
    	{
    		foreach($events as $event)
    		{
    			$event->delete();
    		}
    		return 'logs deleted';
    	}
    	return 'no logs';
    }
}
