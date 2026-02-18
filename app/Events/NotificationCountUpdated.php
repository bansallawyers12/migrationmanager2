<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCountUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $receiverId;
    public $unreadCount;

    /**
     * Create a new event instance.
     *
     * @param int $receiverId
     * @param int $unreadCount
     */
    public function __construct($receiverId, $unreadCount)
    {
        $this->receiverId = $receiverId;
        $this->unreadCount = (int) $unreadCount;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->receiverId);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'receiver_id' => $this->receiverId,
            'unread_count' => $this->unreadCount,
            'timestamp' => now()->toISOString(),
            'type' => 'notification_count_updated'
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'notification.count.updated';
    }
}
