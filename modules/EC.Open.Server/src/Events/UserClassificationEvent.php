<?php

namespace GuoJiangClub\EC\Open\Server\Events;

use GuoJiangClub\Component\User\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserClassificationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $classifications;
    protected $user_id;

    /**
     * Create a new event instance.
     *
     * @param array $classifications
     * @param int $userId
     */
    public function __construct(array $classifications, int $userId)
    {
        $this->classifications = $classifications;
        $this->user_id = $userId;
    }

    public function getClassifications()
    {
        return $this->classifications;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

}
