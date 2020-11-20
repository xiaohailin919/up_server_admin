<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MySql\MGroup as MgModel;
use App\Models\MySql\MGroupRelationship as MgrModel;


/**
 * Class AutoDestroyMgr
 * @package App\Console\Commands
 * @deprecated
 */
class AutoDestroyMgr extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:destroy_mgr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deprecated!!! Auto destroy mgroup relationship';

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
        $mgModel = new MgModel();
        $mgrModel = new MgrModel();

        // 获取已删除的mgroup
        $mg = $mgModel->queryBuilder()
            ->where('status', 0)
            ->orderBy('update_time', 'desc')
            ->limit(100)
            ->get();

        foreach($mg as $val){
            // 删除mgroup对应的关系表数据
            $mgrModel->queryBuilder()->where('mgroup_id', $val['id'])->delete();
        }
    }
}
