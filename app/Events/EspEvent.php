<?php

namespace App\Events;

use App\Models\Esp;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EspEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $esp;
    /**
     * Create a new event instance.
     */
    public function __construct(Esp $esp)
    {
        $this->esp = $esp;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new Channel('esp.' . $this->esp->id);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->esp->id,
            'lang' => $this->esp->lang,
            'lat' => $this->esp->lat,
            'battery_percentage' => $this->esp->battery_percentage,
            'name' => $this->esp->name,
            'is_online' => $this->esp->is_online,
        ];
    }

    public function broadcastAs()
    {
        return 'esp.' . $this->esp->id . '.updated';
    }
}
