<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserConnectionAdded extends Event implements ShouldBroadcast
{
    use SerializesModels;
    public $newConnection;
    private $username;
    public  $type = 'new_connection';

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($newConnection, $username)
    {
        $this->username = $username;
        $this->newConnection = $newConnection;
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
