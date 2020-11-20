<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use App\Models\MySql\UGroup as UGroupMyModel;
use App\Models\MySql\MGroupRelationship as MgRelationshipMyModel;

/**
 * Class AutoDestroyUgroup
 * @package App\Console\Commands
 * @deprecated
 */
class AutoDestroyUgroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:destroy_ugroup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deprecated!!! Auto destroy ugroup';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $time = strtotime('-180 days');
        $ugroup = DB::table('ugroup as u')
            ->leftJoin('mgroup_relationship as r', 'r.ugroup_id', '=', 'u.id')
            ->select('u.id as id')
            ->where('u.update_time', '<', $time)
            ->whereRaw('r.id is NULL')
            ->limit(100)
            ->get();
        if(count($ugroup) <= 0){
            return true;
        }
        foreach($ugroup as $val){
//            echo $val['id'] . "\n";
            $id = $val['id'];
            if($id <= 0){
                continue;
            }
            (new UGroupMyModel())->queryBuilder()
                ->where('id', $val['id'])
                ->where('update_time', '<', $time)
                ->delete();
        }
        return true;
    }
}
