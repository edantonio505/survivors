<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use Storage;
use Image;

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
    		$file = $request->file('file');
    		$name = time().'avatar'.$user->name.$file->getClientOriginalName();
           	$path = 'https://s3-us-west-2.amazonaws.com/edantonio505-survivors-network/';
           	$user->avatar = $path.$name;
           	$user->save();
           	$thumbnail = Image::make($file)->fit(350, 350);
           	Storage::disk('s3')->put('/'.$name, $thumbnail->response()->content());

           	return response()->json(['new_avatar' => $path.$name]);
    	}

    }
}
