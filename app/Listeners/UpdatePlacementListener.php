<?php

namespace App\Listeners;

use App\Events\UpdatePlacement;
use App\Services\QueueSync;

class UpdatePlacementListener
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
     * @param  UpdatePlacement  $event
     * @return void
     */
    public function handle(UpdatePlacement $event)
    {
        //写入mongo待同步队列
//        $this->addToSyncQueue($event);
    }

    /**
     * 写入mongo待同步队列
     * @param UpdatePlacement UpdatePlacement
     */
    private function addToSyncQueue(UpdatePlacement $event)
    {
        $queue = new QueueSync('placement');
        $queue->dispatch($event->placementId);
    }
}
