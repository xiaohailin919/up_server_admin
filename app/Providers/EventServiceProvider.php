<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Database\Events\StatementPrepared;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\UpdatePublisher' => [
            'App\Listeners\UpdatePublisherListener',
            'App\Listeners\CreateNetwork',
        ],

        'App\Events\UpdateApp' => [
            'App\Listeners\UpdateAppListener',
        ],

        'App\Events\UpdatePlacement' => [
            'App\Listeners\UpdatePlacementListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
        Event::listen(StatementPrepared::class, function ($event) {
            $event->statement->setFetchMode(\PDO::FETCH_ASSOC);
        });
    }
}
