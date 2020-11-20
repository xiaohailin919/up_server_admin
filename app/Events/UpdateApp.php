<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class UpdateApp
{
    use SerializesModels;

    public $appId;

    /**
     * Create a new event instance.
     * @param Integer $appId
     */
    public function __construct($appId)
    {
        $this->appId = $appId;
    }
}
