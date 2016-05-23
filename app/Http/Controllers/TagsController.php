<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Tag;
use Response;

class TagsController extends Controller
{
    public function getTags(Request $request)
    {	
    	$name = str_replace("#", "", $request->input('name'));
    	$tag = new Tag();
        $tag->name = $name;
        $tag->save();
        return $tag->name;
    }
}
