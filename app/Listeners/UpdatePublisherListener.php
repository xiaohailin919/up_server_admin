<?php

namespace App\Listeners;

use App\Events\UpdatePublisher;
use App\Services\QueueSync;

class UpdatePublisherListener
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
     * @param  UpdatePublisher  $event
     * @return void
     */
    public function handle(UpdatePublisher $event)
    {
        //写入mongo待同步队列
        $this->addToSyncQueue($event);
    }

    /**
     * 写入mongo待同步队列
     * @param UpdatePublisher $event
     */
    private function addToSyncQueue(UpdatePublisher $event)
    {
        $queue = new QueueSync('publisher');
        $queue->dispatch($event->publisherId);
    }
}
