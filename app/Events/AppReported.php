<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppReported
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $appVersionId;
    private $eventCode;
    private $subEventCode;
    private $clientIp;
    private $deviceId;
    private $langCode;
    private $domain;

    /**
     * Create a new event instance.
     */
    public function __construct($appVersionId, $eventCode, $subEventCode, $clientIp, $deviceId, $langCode, $domain)
    {
        $this->appVersionId = $appVersionId;
        $this->eventCode = $eventCode;
        $this->subEventCode = $subEventCode;
        $this->clientIp = $clientIp;
        $this->deviceId = $deviceId;
        $this->langCode = $langCode;
        $this->domain = $domain;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
