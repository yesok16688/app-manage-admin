<?php

namespace App\Listeners;

use App\Events\AppReported;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogAppEvent implements ShouldQueue
{
    public $connection = 'redis';
    public $queue = 'app-event';

    public $tries = 1;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AppReported $event): void
    {
        //
    }
}
