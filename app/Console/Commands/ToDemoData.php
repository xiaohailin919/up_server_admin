<?php
/**
 * Created by PhpStorm.
 * User: SA
 * Date: 2018/12/14
 * Time: 17:50
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\ToolDemoData as ToolDemoDataService;

/**
 * Class ToDemoData
 * @package App\Console\Commands
 * @deprecated
 */
class ToDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tool:to_demo_data {day=day}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deprecated!!! command tool to_demo_data';

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
        $day = $this->argument('day');
        if ($day == 'day') {
            $day = date('Ymd');
        }
        
        if ($day < 20181208) {
            echo "day param is must max 20181208";
            return;
        }
        ToolDemoDataService::toDealDemoReport($day);
    }
}