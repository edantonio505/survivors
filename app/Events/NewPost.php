<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewPost extends Event implements ShouldBroadcast
{
    use SerializesModels;
    public $userCreator;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userCreator)
    {
        $this->userCreator = $userCreator;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['all.users'];
    }
}
