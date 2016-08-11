<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Http\Requests;
use App\TopicOfTheDay;
use App\Tag;
use App\TopicOfTheDayTitle;
use App\User;
use Storage;
use Image;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Format\Video\X264;
use App\Helpers\EventsNotifications;
use File;
use App\Events\NewPost;
use App\Events\UserUninspired;
use DB;
use App\ReportedUsers;

class MobileTopicController extends Controller
{	


	public function __construct(){
	    $this->middleware('jwt.auth');
	}

    /*=======================================================================================
                                    GET ALL POSTS
    =========================================================================================*/	
   	public function index($authUser)
   	{
      $data = [];
      $user = User::where('email', $authUser)->first();
   		$topicsOfTheDay = TopicOfTheDay::orderBy('created_at', 'desc')->paginate(10);
   		$topics = [];
  		$topics['total'] = $topicsOfTheDay->total();
  		$topics['per_page'] = $topicsOfTheDay->perPage();
  		$topics['current_page'] = $topicsOfTheDay->currentPage();
  		$topics['last_page'] = $topicsOfTheDay->lastPage();
  		$topics['next_page_url'] = $topicsOfTheDay->nextPageUrl();
  		$topics['prev_page_url'] = $topicsOfTheDay->previousPageUrl();
  		$topics['from'] = $topicsOfTheDay->toArray()['from'];
  		$topics['to'] = $topicsOfTheDay->toArray()['to'];
  		$topics['data'] = [];
  		
  		foreach($topicsOfTheDay as $topicDay)
  		{	
        $topic = $this->TopicToArray($topicDay, $user);

        if($topic != null)
        {
          $data[] = $topic;
        }
  		}
		  $topics['data'] = $data;
   		return Response::json($topics, 200);
   	}
    // =====================================================================================





    /*=========================================================================================
                                  GET OPTIONS FOR CREATING NEW POSTS
    ==========================================================================================*/
   	public function create()
   	{
   		$topicTitle = TopicOfTheDayTitle::all();
   		$t = Tag::all();
      $tags = array();
      foreach($t as $tag)
      {
        $ts['text'] = '#'.$tag->name;
        $tags[] = $ts;
      }

      return response()->json(['tags' => $tags, 'topicTitle' => $topicTitle]);
   	}
    // ======================================================================================






    /*============================================================================================
                                            STORING A NEW POST
    ==============================================================================================*/
   	public function store(Request $request)
   	{ 
   		$user = User::where('email', $request->input('email'))->first();

      /*-------------------------------Avoid for banned user to create posts-----------------*/
      $r = ReportedUsers::where('user_id', $user->id)->first();
      if(isset($r) && $r->blocked != 0){return 'banned';}
      // --------------------------------------------------------------------------------------

   		$topicTitle = TopicOfTheDayTitle::where('topic_title', $request->input('topic_title'))->first();
      $tagAttach = [];
      $inputTags = $request->input('tags');
      if($request->hasFile('video') || $request->hasFile('file'))
      {
        $inputTags = json_decode($request->input('tags'), true);
      }
      if(count($inputTags) > 0)
      {
        foreach($inputTags as $tag)
        {
          $newtag = Tag::firstOrCreate(['name' => str_replace("#", "", $tag['text'])]);
          $tagAttach[] = $newtag->id;
        }
      }
      $en = new EventsNotifications();
      event(new NewPost($user->name));
   		$topic = $user->topics()->create([
   			'title' => $request->input('title'),
   			'body' => $request->input('body'),
   			'slug' => $request->input('slug'),
   			'topic_title_id' => $topicTitle->id
   		]);
   		$topic->tags()->attach($tagAttach);
      $en->ConnectioPostedNewTopic($user, $topic->id);

        // ------------------------------------------------------------------------------
        //                            UPLOAD FILES(PICTURES OR VIDEOS)
        // -----------------------------------------------------------------------------
   		   if($request->hasFile('video'))
        {   
            $video = $request->file('video');
            $name = time().$topic->slug.$topic->user->name.$video->getClientOriginalName();
            $thumbnail_name = time().$topic->slug.$topic->user->name.'.jpg';
            $path = 'https://s3-us-west-2.amazonaws.com/edantonio505-survivors-network/';
            
            $content = file_get_contents($video->getRealPath());
            $ffmpeg = FFMpeg::create(array(
                'ffmpeg.binaries'  => '/home/forge/ffmpeg/ffmpeg',
                'ffprobe.binaries' => '/home/forge/ffmpeg/ffprobe',
                'timeout'          => 3600,
                'ffmpeg.threads'   => 12,
            ));
            $videoInput = $ffmpeg->open($video->getRealPath());

            
            // -------------------------------CREATE A VIDEO THUMBNAIL-------------------
            $frame = $videoInput->frame(TimeCode::fromSeconds(2))->save($thumbnail_name);
            

            // ---------------------------------------------------------------------------
            //                 IF VIDEO IS NOT MP4 TRANSFORM VIDEO TO MP4
            // ---------------------------------------------------------------------------
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


            // -------------------------------------------------------------------------------
            //                      SAVE VIDEO IN AMAZON S3
            // -------------------------------------------------------------------------------
            Storage::disk('s3')->put('/'.$thumbnail_name, $thumbnail_content->response()->content());
            Storage::disk('s3')->put('/'.$name, $content);
            File::delete($thumbnail_name);
            File::delete($name);
        }
   		if ($request->hasFile('file')) {
           $file = $request->file('file');
           $name = time().$topic->slug.$topic->user->name.$file->getClientOriginalName();
           $path = 'https://s3-us-west-2.amazonaws.com/edantonio505-survivors-network/';
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
   		return 'success';
   	}
    // ======================================================================================





    /*========================================================================================
                                    CREATING A NEW CONNECTION BETWEN USERS
    ==========================================================================================*/
    public function addConnection(Request $request)
    {   
      $Authenticated = User::where('email', $request->input('authenticated'))->first();
      $user = User::where('name', $request->input('newConnection'))->first();
      $Authenticated->addConnection($user);
      return 'connection requested';
    }
    // ======================================================================================





    /*=======================================================================================
                                    ACCEPTING A CONNECTION REQUEST
    ========================================================================================*/
    public function acceptConnection(Request $request)
    {
      $AuthUser = User::where('email', $request->input('authenticated'))->first();
      $user = User::where('name', $request->input('acceptConnectionFrom'))->first();
      $AuthUser->acceptConnectionRequest($user);
      return 'Connected';
    }
    // ======================================================================================





    /*========================================================================================
                          GET ALL USERS CONNECTED TO THE AUTHENTICATED USER
    ==========================================================================================*/
    public function getConnections(Request $request)
    {
      $connections = User::where('email', $request->input('AuthEmail'))->first()->connections()->sortBy('name');
      $connectionsReturn = [];
      foreach($connections as $connection)
      { 
        $r = ReportedUsers::where('user_id', $connection->id)->first();
        
        if(!$r || (isset($r) && $r->blocked ==0)){
          $con['id'] = $connection->id;
          $con['name'] = $connection->name;
          $con['avatar'] = $connection->getAvatarListUrl();
          $connectionsReturn[] = $con;
        }
      }
      return response()->json($connectionsReturn);
    }

    public function getUserById($id)
    {
      $user = User::findOrFail($id);
      $userR = [];
      $userR['name'] = $user->name;
      $userR['first_name'] = $user->first_name;
      $userR['last_name'] = $user->last_name;
      $userR['email'] = $user->email;
      $userR['location'] = $user->location;
      $userR['gender'] = $user->gender;
      $userR['birthday'] = $user->birthday;
      $userR['avatar'] = $user->getAvatarProfileUrl();
      $userR['topics_count'] = $user->topics->count();
      $userR['inspired_count'] = $user->inspiredCount();
      $userR['connections_count'] = $user->connections()->count();
      $userR['topics'] = $this->userTopicsArray($user->Topics);
      $response['user'] = $userR;
      return response()->json($response);
    }


    public function inspiresUser($id, $authEmail)
    {
      $user = User::where('email', $authEmail)->first();
      $topic = TopicOfTheDay::findOrFail($id);
      $en = new EventsNotifications();
      if($user->checkInspirationTopic($topic))
      {
          return 'already inspired';
      }
      $inspiration = $topic->inspired()->create([]);
      $user->isInspiredBy()->save($inspiration);
      $en->userIsInspired($topic, $user->name);
      return 'inspired successfully';
    }

    public function uninspiresUser($id, $authEmail)
    {
      $user = User::where('email', $authEmail)->first();
      $topic = TopicOfTheDay::findOrFail($id);
      event(new UserUninspired($topic->user->name));
      $inspiration = $topic->inspired->where('user_id', $user->id)->first();
      $inspiration->delete();
      return 'uninspired successfully';
    }

    public function getComments($id)
    {
      $body = TopicOfTheDay::findOrFail($id);
      $comments = [];
      foreach($body->comments as $comment)
      {
        $c['body'] = $comment->body;
        $c['user_name'] = $comment->user()->name;
        $c['user_avatar'] = $comment->user()->getAvatarListUrl();
        $c['created_time'] = $comment->created_at->diffForHumans();
        $comments[] = $c;
      }

      return response()->json(['body' => $body->body, 'post_user' => $body->user->name, 'post_user_avatar' => $body->user->getAvatarListUrl(), 'created_time' => $body->created_at->diffForHumans(),'tags' => $body->tags, 'comments' => $comments]);
    }

    public function postComment($id, $authEmail, Request $request)
    {
      $topic = TopicOfTheDay::findOrFail($id);
      $user = User::where('name', $authEmail)->first();
      $en = new EventsNotifications();
      $topic->comments()->create([
            'body' => $request->input('body'),
            'user_id' => $user->id
      ]);
      $en->NotifyAllUsersForNewComment($topic, $user->name);
      return 'Commented successfully';
    }

    public function getUserByName($name, $AuthUserEmail)
    {
      return response()->json($this->returnUserandConnectionStatus(User::where('name', $name)->first(), User::where('email', $AuthUserEmail)->first()));      
    }

    public function getUser($id, $auth)
    {
      return response()->json($this->returnUserandConnectionStatus(TopicOfTheDay::findOrFail($id)->user, User::where('email', $auth)->first()));
    }


    public function getTopic($id, $AuthEmail)
    {      
      return response()->json($this->TopicToArray(TopicOfTheDay::findOrFail($id), User::where('email', $AuthEmail)->first()));
    }


    public function searchBy($input)
    { 
      $users = User::all();

      foreach($users as $user)
      { 
        $r = ReportedUsers::where('user_id', $user->id)->first();
        if(!$r || (isset($r) && $r->blocked == 0)){
          $freeUsers[] = $user;
        }
      }

      $tags = Tag::all();
      if($input == 'name')
      {
        return $freeUsers;
      }

      return $tags;
    }



    public function getCategories($name)
    {
      $tags = Tag::where('name', $name)->first()->topics;
      $tagsResponse = $this->userTopicsArray($tags);

      return $tagsResponse;
    }




    // This Controllers methods--------------------------------------------------------
     private function returnUserandConnectionStatus($user, $AuthUser)
    {
      $userR = [];
      $userR['name'] = $user->name;
      $userR['first_name'] = $user->first_name;
      $userR['last_name'] = $user->last_name;
      $userR['email'] = $user->email;
      $userR['location'] = $user->location;
      $userR['gender'] = $user->gender;
      $userR['birthday'] = $user->birthday;
      $userR['avatar'] = $user->getAvatarProfileUrl();
      $userR['topics_count'] = $user->topics->count();
      $userR['inspired_count'] = $user->inspiredCount();
      $userR['connections_count'] = $user->connections()->count();
      $userR['status'] = $this->checkStatus($user, $AuthUser);
      $userR['topics'] = $this->userTopicsArray($user->Topics);
      $response['user'] = $userR;
      return $response;
    }

    private function userTopicsArray($topics)
    {
      $data= [];
      foreach($topics as $topicDay)
      { 
        $topic['id'] = $topicDay->id;
        if($topicDay->checkIfHasPhotos() && $topicDay->checkIfHasVideo() !=  True){
          $topic['picture'] = $topicDay->photos->first()->path;
          $topic['pic_thumbnail'] = $topicDay->photos->first()->thumbnail_path;
        } else {
          $topic['picture'] = "";
          $topic['pic_thumbnail'] = "";
        }
        if($topicDay->checkIfHasVideo() && $topicDay->checkIfHasPhotos() != true){
          $topic['video'] = $topicDay->video;
          $topic['video_thumbnail'] = $topicDay->video_thumbnail;
        } else {
          $topic['video'] = "";
          $topic['video_thumbnail'] = "";
        }
        $data[] = $topic;   
      }
      return $data;
    }

    private function checkStatus($user, $AuthUser)
    { 
      if($AuthUser->hasConnectionRequestPending($user)){
        return 'Waiting';
      }
      if($AuthUser->hasConnectionRequestReceived($user))
      {
        return 'Accept';
      }
      if($AuthUser->isConnectionsWith($user))
      {
        return 'Connected';
      }
      return 'Connect';
    }

    private function TopicToArray($topicDay, $user)
    { 
      $r = ReportedUsers::where('user_id', $topicDay->user->id)->first();
      $topic = null;
      if(!$r || (isset($r) && $r->blocked == 0)) {
        $topic['id'] = $topicDay->id;
        $topic['title'] = $topicDay->title;
        $topic['category'] = TopicOfTheDayTitle::findOrFail($topicDay->topic_title_id)->topic_title;
        if($topicDay->checkIfHasPhotos() && $topicDay->checkIfHasVideo() !=  True){
          $topic['picture'] = $topicDay->photos->first()->path;
          $topic['pic_thumbnail'] = $topicDay->photos->first()->thumbnail_path;
        } else {
          $topic['picture'] = "";
          $topic['pic_thumbnail'] = "";
        }
        if($topicDay->checkIfHasVideo() && $topicDay->checkIfHasPhotos() != true){
          $topic['video'] = $topicDay->video;
          $topic['video_thumbnail'] = $topicDay->video_thumbnail;
        } else {
          $topic['video'] = "";
          $topic['video_thumbnail'] = "";
        }
        $topic['tags'] = $topicDay->tags;
        $topic['inspires'] = $topicDay->inspired->count();
        $topic['summary'] = substr($topicDay->body, 0, 100);
        $topic['body'] = $topicDay->body;
        $topic['user_name'] = $topicDay->user->name;
        $topic['user_avatar'] = $topicDay->user->getAvatarListUrl();
        $topic['created_time'] = $topicDay->created_at->diffForHumans();
        $topic['inspiredBy'] = $user->checkInspirationTopic($topicDay);
        $topic['comments_ammount'] = $topicDay->comments->count();
      }

      
      return $topic;
    }
}
