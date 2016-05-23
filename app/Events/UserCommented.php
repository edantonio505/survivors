<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserCommented extends Event implements ShouldBroadcast
{
    use SerializesModels;
    public $userCommenter;
    private $userCreator;
    public $topic_id;
    public $type = 'new_comment';

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userCommenter, $topic_id, $userCreator)
    {
        $this->userCommenter = $userCommenter;
        $this->topic_id = $topic_id;
        $this->userCreator = $userCreator;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['user.'.$this->userCreator];
    }
}
