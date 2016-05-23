<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ConnectionCreatedPost extends Event implements ShouldBroadcast
{
    use SerializesModels;
    private $username;
    public $connectionPosting;
    public $topic_id;
    public $type = 'connections_newpost';

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($connectionPosting, $username, $topic_id)
    {
        $this->username = $username;
        $this->topic_id = $topic_id;
        $this->connectionPosting = $connectionPosting;
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
