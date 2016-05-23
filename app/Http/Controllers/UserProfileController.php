<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use App\User;

class UserProfileController extends Controller
{
    public function addConnection(Request $request)
    {	
    	$user = User::where('name', $request->input('username'))->first();
    	
    	Auth::user()->addConnection($user);

    	return redirect()->back();
    }

    public function acceptConnection(Request $request)
    {	
    	$user = User::where('name', $request->input('username'))->first();
    	Auth::user()->acceptConnectionRequest($user);
    	return redirect()->back();
    }

    public function connectionRequests()
    {	
    	$requests = Auth::user()->connectionRequests();
    	return view('admin.requests')->withRequests($requests);
    }

    public function connections()
    {
    	return view('pages.connections');
    }
}
