<?php

namespace Dion\Foa\Events;

use Dion\Foa\Models\ObjectType;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DataDefined
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data = [];

    public $objectType;

    /**
     * DataDefined constructor.
     * @param ObjectType $objectType
     * @param array $data
     */
    public function __construct(ObjectType $objectType, array $data = [])
    {
        $this->objectType = $objectType;

        $this->data = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
