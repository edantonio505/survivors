<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;

class ProfileController extends Controller
{
    public function update(Request $request)
    {	
    	$newName = $request->input('newUsername');
    	$user = User::where('email', $request->input('email'))->first();
    	$already_exists = (bool) User::where('name', $newName)->first();

    	if($request->input('newUsername') && $newName != $user->name && $newName != ''){
    		if(!$already_exists)
    		{
    			$user->name = $newName;
    			$user->save();
    			return 'saved successfully';
    		}
    		return 'Name is taken';
    	}

    	if($request->hasFile('file'))
    	{
    		return 'YAY!';
    	}

    }
}
