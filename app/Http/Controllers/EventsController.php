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

    public function signupOauth(Request $request)
    {
        $q = 'https://www.googleapis.com/oauth2/v1/userinfo?access_token='.$request->input('access_token');
        $json = file_get_contents($q);
        $userInfoArray = json_decode($json,true);

        return response()->json($userInfoArray);
    }
}
