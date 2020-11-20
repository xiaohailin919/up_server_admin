<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Events\UpdatePublisher;

use App\Models\MySql\Publisher;
use App\Models\MySql\NetworkFirm;
use App\Models\MySql\Network;

class CreateNetwork
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
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if($event instanceof UpdatePublisher){
            $this->ifUpdatePublisher($event);
        }
    }

    /**
     * 如果订阅的事件是 UpdatePublisher
     *
     * @param UpdatePublisher $event
     */
    private function ifUpdatePublisher(UpdatePublisher $event){
        $publisherId = $event->publisherId;
        $publisher   = $event->publisher;
        if(isset($publisher['adx_switch']) && $publisher['adx_switch'] == Publisher::ADX_SWITCH_ON){
            $networkExist = Network::query()
                ->where("publisher_id", $publisherId)
                ->where("nw_firm_id", NetworkFirm::ADX)
                ->exists();
            if(!$networkExist){
                $data = [
                    'name'            => "TopOn ADX",
                    'api_version'     => 1,
                    'status'          => 3,
                    'app_id'          => 0,
                    'open_api_status' => 3,
                    'update_time'     => time(),
                    'unit_switch'     => 2,
                    'publisher_id'    => $publisherId,
                    'nw_firm_id'      => NetworkFirm::ADX,
                    'create_time'     => time(),
                ];
                Network::query()->insert($data);
            }
        }
    }
}
