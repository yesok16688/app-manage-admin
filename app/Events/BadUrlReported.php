<?php

namespace App\Events;

use App\Utils\IPUtils\IPLocateInfo;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BadUrlReported
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $invalidList;
    public string $versionId;
    public ?IPLocateInfo $ipLocation;

    /**
     * Create a new event instance.
     */
    public function __construct($versionId, $invalidList, $ipLocation = null)
    {
        $this->versionId = $versionId;
        $this->invalidList = $invalidList;
        $this->ipLocation = $ipLocation;
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
