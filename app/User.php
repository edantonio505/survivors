<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\TopicOfTheDay;
use App\Inspiration;
use App\Events\UserConnectionAdded;
use App\Events\UserAcceptedConnection;
use App\Helpers\EventsNotifications;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'first_name', 
        'last_name', 
        'location', 
        'gender', 
        'birthday',
        'avatar'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];



    public function EventLogs()
    {
        return $this->hasMany('App\UserEventsLog');
    }

    public function Topics()
    {
        return $this->hasMany('App\TopicOfTheDay');
    }


      public function getAvatarProfileUrl()
    {   
        $emailHash = md5($this->email);
        return "http://www.gravatar.com/avatar/".$emailHash."?d=mm&s=270";
    }


     public function getAvatarListUrl()
    {   
        $emailHash = md5($this->email);
        return "http://www.gravatar.com/avatar/".$emailHash."?d=mm&s=80";
    }

    public function isInspiredBy()
    {
        return $this->hasMany('App\Inspiration');
    }

    public function checkInspirationTopic(TopicOfTheDay $topic)
    {
        return (bool) $topic->inspired->where('user_id', $this->id)->count();
    }

    public function inspiredCount()
    {
        $inspiredCount = 0;
        foreach($this->topics as $topic){
          $number = $topic->inspired->count();
          $inspiredCount = $inspiredCount + $number;
        }
        return $inspiredCount;
    }

    // ----------------------------------------Connections logic-----------------------------------
    public function connectionOfMine(){
        return $this->belongsToMany('App\User', 'connection_user', 'user_id', 'connection_id');
    }
    public function connectionOf()
    {
        return $this->belongsToMany('App\User', 'connection_user', 'connection_id', 'user_id');
    }
    public function connections()
    {
        return $this->connectionOfMine()->wherePivot('accepted', true)->get()->
            merge($this->connectionOf()->wherePivot('accepted', true)->get());
    }
    public function connectionRequests()
    {
        return $this->connectionOfMine()->wherePivot('accepted', false)->get();
    }
    public function connectionRequestsPending()
    {
        return $this->connectionOf()->wherePivot('accepted', false)->get();
    }
    public function hasConnectionRequestPending(User $user)
    {
        return (bool) $this->connectionRequestsPending()->where('id', $user->id)->count();
    }

    public function hasConnectionRequestReceived(User $user)
    {
        return (bool) $this->connectionRequests()->where('id', $user->id)->count();
    }
    public function addConnection(User $user)
    {   
        $this->createLog('new_connection', $user, $this->name);
        event(new UserConnectionAdded($this->name, $user->name));
        $this->connectionOf()->attach($user->id);
    }

    public function acceptConnectionRequest(User $user)
    {   
        event(new UserAcceptedConnection($this->name, $user->name));
        $this->createLog('user_accepted_connection', $user, $this->name);
        
        $this->connectionRequests()->where('id', $user->id)->first()
        ->pivot->update(['accepted'=>true]);
    }
    public function isConnectionsWith(User $user)
    {
        return (bool) $this->connections()->where('id', $user->id)->count();
    }

    private function createLog($type, User $receptor, $emmiter)
    {
        $ev = new EventsNotifications();
        $ev->createEventLog($type, $receptor, $emmiter);
    }
    //-----------------------------------------------------------------------------------------------
}
