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
           	$content = file_get_contents($file->getRealPath());
           	$height = Image::make($file)->height();
           	$width = Image::make($file)->width();
           	$thumbnail = ($height > $width ? Image::make($file)->fit(320, 354) : Image::make($file)->resize(320, null, function ($constraint){$constraint->aspectRatio();}));
           	Storage::disk('s3')->put('/'.$name, $content);

           	return respone()->json(['response' => 'new_pic_success', 'new_avatar' => $path.$path]);
    	}

    }
}
