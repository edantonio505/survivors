<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserIsInspired extends Event implements ShouldBroadcast
{
    use SerializesModels;
    public $inspiredUser;
    private $userCreator;
    public $topic_id;
    public $type = 'user_inspired';

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($inspiredUser,$topic_id, $userCreator)
    {
        $this->userCreator = $userCreator;
        $this->topic_id = $topic_id;
        $this->inspiredUser = $inspiredUser;
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
