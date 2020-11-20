<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class UpdatePublisher
{
    use SerializesModels;

    public $publisherId;
    public $publisher;

    /**
     * UpdatePublisher constructor.
     *
     * @param int   $publisherId
     * @param array $publisher
     */
    public function __construct($publisherId, $publisher = [])
    {
        $this->publisherId = $publisherId;
        $this->publisher   = $publisher;
    }
}
