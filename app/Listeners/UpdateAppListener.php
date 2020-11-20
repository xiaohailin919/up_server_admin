<?php

namespace App\Listeners;

use App\Events\UpdateApp;
use App\Services\QueueSync;

class UpdateAppListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UpdateApp  $event
     * @return void
     */
    public function handle(UpdateApp $event)
    {
        //写入mongo待同步队列
        $this->addToSyncQueue($event);
    }

    /**
     * 写入mongo待同步队列
     * @param UpdateApp $event
     */
    private function addToSyncQueue(UpdateApp $event)
    {
        $queue = new QueueSync('app');
        $queue->dispatch($event->appId);
    }
}
