<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DictNetwork as DictNetworkService;

class DictNetwork extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:dict_network';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Dict Network data to MongoDB';

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
        DictNetworkService::syncToMo();
    }
}
