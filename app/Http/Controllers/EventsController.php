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
        $data = json_decode($json,true);

        $parts = explode("@", $data['email']);
        $name = $parts[0];


        // $user =  User::create([
        //     'name' => $name,
        //     'email' => $data['email'],
        //     'first_name' => $data['given_name'],
        //     'last_name'  => $data['family_name'],
        //     'gender' =>  $data['gender'],
        //     'avatar' => $data['picture']
        // ]);

        return response()->json([
            'name' => $name, 
            'email' => $data['email'], 
            'first_name' => $data['given_name'],
            'last_name' => $data['family_name'],
            'gender' => $data['gender'],
            'avatar' => $data['picture']
        ]);
    }
}
