<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Http\Requests;
use App\TopicOfTheDay;
use App\User;
use Image;
use Storage;
use App\Tag;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Format\Video\X264;
use File;
use App\Helpers\EventsNotifications;
use App\Events\NewPost;
use App\Events\UserUninspired;

class PostController extends Controller
{   

    public function createTopic(Request $request)
    {	
        $tags = Tag::all();
        $tagAttach = [];
        $en = new EventsNotifications();
        event(new NewPost(Auth::user()->name));
        if(count($request->input('tags')) > 0) {
            foreach($tags as $tag)
            {   
                foreach($request->input('tags') as $tagName)
                {
                    if(str_replace("#", "", $tagName) === $tag->name)
                    {
                        $tagAttach[] = $tag->id;
                    }
                }
            }  
        }
        

        $topic = Auth::user()->Topics()->create($request->input());
        $topic->tags()->attach($tagAttach);
        
        $en->ConnectioPostedNewTopic(Auth::user(), $topic->id);
        if($request->hasFile('video'))
        {   
            $video = $request->file('video');
            $name = time().$topic->slug.$topic->user->name.$video->getClientOriginalName();
            $thumbnail_name = time().$topic->slug.$topic->user->name.'.jpg';
            $path = 'https://s3-us-west-2.amazonaws.com/speakout-survivorsnetwork/';
            

            $content = file_get_contents($video->getRealPath());
            $ffmpeg = FFMpeg::create(array(
                'ffmpeg.binaries'  => '/home/forge/ffmpeg/ffmpeg',
                'ffprobe.binaries' => '/home/forge/ffmpeg/ffprobe',
                // 'ffmpeg.binaries'  => '/home/vagrant/ffmpeg/ffmpeg',
                // 'ffprobe.binaries' => '/home/vagrant/ffmpeg/ffprobe',
                'timeout'          => 3600,
                'ffmpeg.threads'   => 12,
            ));
            $ffmpeg->getFFMpegDriver()->listen(new \Alchemy\BinaryDriver\Listeners\DebugListener());
            $ffmpeg->getFFMpegDriver()->on('debug', function ($message) {
                echo $message."\n";
            });

            $videoInput = $ffmpeg->open($video->getRealPath());
            $frame = $videoInput->frame(TimeCode::fromSeconds(2))->save($thumbnail_name);

            if($video->getMimeType() != 'video/mp4')
            {   
                $name = str_replace(".".pathinfo($name, PATHINFO_EXTENSION),'.mp4', $name);
                $videoInput->save(new X264(), $name);
                $content = file_get_contents($name);
            }


            $topic->video = $path.$name;
            $topic->video_thumbnail = $path.$thumbnail_name;
            $topic->save();


            $thumbnail_content = Image::make($thumbnail_name)->resize(320, null, function ($constraint) {
                $constraint->aspectRatio();
            });


            Storage::disk('s3')->put('/'.$thumbnail_name, $thumbnail_content->response()->content());
            Storage::disk('s3')->put('/'.$name, $content);
            File::delete($thumbnail_name);
            File::delete($name);
        }

        

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $name = time().$topic->slug.$topic->user->name.$file->getClientOriginalName();
            $path = 'https://s3-us-west-2.amazonaws.com/speakout-survivorsnetwork/';
            $photo = $topic->photos()->create([
                'name' => $name,
                'path' => $path.$name,
                'thumbnail_path' => $path.'tn-'.$name
            ]);
            $content = file_get_contents($file->getRealPath());
            $height = Image::make($file)->height();
            $width = Image::make($file)->width();
            $thumbnail = ($height > $width ? Image::make($file)->fit(320, 354) : Image::make($file)->resize(320, null, function ($constraint){$constraint->aspectRatio();}));
            Storage::disk('s3')->put('/'.$name, $content);
            Storage::disk('s3')->put('/tn-'.$name, $thumbnail->response()->content());
        }

    	return redirect()->back();
    }



    public function topicPage($slug, Request $request)
    {	
        $user = User::where('name', $request->input('by'))->first();
        $topic = TopicOfTheDay::where('slug', $slug)
        ->where('user_id', $user->id)->first();
    	return view('pages.topicpage')->withTopic($topic);
    }


    public function postComment(Request $request)
    {   
        $en = new EventsNotifications();
        $topic = TopicOfTheDay::FindOrFail($request->input('id'));
        $topic->comments()->create([
            'body' => $request->input('body'),
            'user_id' => Auth::user()->id
        ]);
        $en->NotifyAllUsersForNewComment($topic);
        return redirect()->back();
    }



    public function inspiresUser($id)
    {
        $topic = TopicOfTheDay::findOrFail($id);
        $en = new EventsNotifications();
        if(Auth::user()->checkInspirationTopic($topic))
        {
            return redirect()->back();
        }
        $inspiration = $topic->inspired()->create([]);
        Auth::user()->isInspiredBy()->save($inspiration);
        $en->userIsInspired($topic);
        return 'You are inspired by this';
    }

    public function uninspiresUser($id)
    {
        $topic = TopicOfTheDay::findOrFail($id);
        event(new UserUninspired($topic->user->name));
        $inspiration = $topic->inspired->where('user_id', Auth::user()->id)->first();
        $inspiration->delete();
        return 'This does not inspire you';
    }
}
