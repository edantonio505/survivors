<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;

class AuthenticateController extends Controller
{	
	public function __construct()
   	{
       $this->middleware('jwt.auth', ['except' => ['authenticate']]);
   	}


   public function index()
   {
   	return "Auth index";
   }

   public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
 
        try {
            // verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
 
        // if no errors are encountered we can return a JWT
        $user = User::where('email', $request->input('email'))->first();

        $logs = [];
        foreach($user->EventLogs->sortByDesc('id') as $log)
        { 
          if($log->type == 'new_comment')
          {
            $event['id'] = $log->id;
            $event['type'] = $log->type;
            $event['topic_id'] = $log->topic_id;
            $event['userCommenter'] = $log->userCommenter;
            $event['inspiredUser'] = null;
            $event['connectionPosting'] = null;
            $event['newConnection'] = null;
            $event['AcceptingUser'] = null;
            $event['connectionPosting'] = null;
          }

          if($log->type == 'user_inspired')
          {
            $event['id'] = $log->id;
            $event['type'] = $log->type;
            $event['topic_id'] = $log->topic_id;
            $event['userCommenter'] = null;
            $event['inspiredUser'] = $log->inspiredUser;
            $event['connectionPosting'] = null;
            $event['newConnection'] = null;
            $event['AcceptingUser'] = null;
            $event['connectionPosting'] = null;
          }

          if($log->type == 'new_connection')
          {
            $event['id'] = $log->id;
            $event['type'] = $log->type;
            $event['topic_id'] = null;
            $event['userCommenter'] = null;
            $event['inspiredUser'] = null;
            $event['connectionPosting'] = null;
            $event['newConnection'] = $log->newConnection;
            $event['AcceptingUser'] = null;
            $event['connectionPosting'] = null;
          }

          if($log->type == 'user_accepted_connection')
          {
            $event['id'] = $log->id;
            $event['type'] = $log->type;
            $event['topic_id'] = null;
            $event['userCommenter'] = null;
            $event['inspiredUser'] = null;
            $event['connectionPosting'] = null;
            $event['newConnection'] = null;
            $event['AcceptingUser'] = $log->AcceptingUser;
            $event['connectionPosting'] = null;
          }

          if($log->type == 'connections_newpost')
          {
            $event['id'] = $log->id;
            $event['type'] = $log->type;
            $event['topic_id'] = $log->topic_id;
            $event['userCommenter'] = null;
            $event['inspiredUser'] = null;
            $event['connectionPosting'] = null;
            $event['newConnection'] = null;
            $event['AcceptingUser'] = null;
            $event['connectionPosting'] = $log->connectionPosting;
          }

          $logs[] = $event;
        }

        return response()->json([
          'token' => $token, 
          'user_name' => $user->name, 
          'user_avatar' => $user->getAvatarListUrl(),
          'event_logs' => $logs,
          'log_count' => count($logs)
        ]);
    }


    public function getAuthenticatedUser()
    {
        try {
 
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
 
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
 
            return response()->json(['token_expired'], $e->getStatusCode());
 
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
 
            return response()->json(['token_invalid'], $e->getStatusCode());
 
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
 
            return response()->json(['token_absent'], $e->getStatusCode());
 
        }
        


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
        $userR['connections_count'] = $user->connections()->count();
        $userR['inspired_count'] = $user->inspiredCount();
        $userR['topics'] = $this->userTopicsArray($user->Topics);
        $response['user'] = $userR;
        return response()->json($response);
    }

    private function userTopicsArray($topics)
    { 
      $data = [];
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
	
}
