<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class UpdatePlacement
{
    use SerializesModels;

    public $placementId;

    /**
     * Create a new event instance.
     * @param Integer $placementId
     */
    public function __construct($placementId)
    {
        $this->placementId = $placementId;
    }
}
