<?php

namespace GuoJiangClub\EC\Open\Server\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserClassification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $classifications;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($classifications)
    {
        $this->classifications = $classifications;
    }

    public function getClassifications()
    {
        return $this->classifications;
    }


}
