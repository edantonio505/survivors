<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserAcceptedConnection extends Event implements ShouldBroadcast
{
    use SerializesModels;
    private $username;
    public $AcceptingUser;
    public $type = 'user_accepted_connection';

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($AcceptingUser, $username)
    {
        $this->username = $username;
        $this->AcceptingUser = $AcceptingUser;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['user.'.$this->username];
    }
}
