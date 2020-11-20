<?php
/**
 * Created by PhpStorm.
 * User: SA
 * Date: 2019/1/23
 * Time: 14:24
 */

namespace App\Console\Commands;

use App\Services\DealBlackRevenueService;
use Illuminate\Console\Command;
use App\Models\MySql\Publisher as PublisherModel;
use App\Models\MySql\Placement as PlacementModel;

/**
 * Class ToolDealBlackRevenue
 * @package App\Console\Commands
 * @deprecated
 */
class ToolDealBlackRevenue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tool:deal_black_revenue {day=day} {user=user} {placement=placement}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deprecated!!! command tool deal_black_revenue';

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
            $day = date('Ymd', strtotime("-2 day"));
        }
        $user = $this->argument('user');
        $placement = $this->argument('placement');
        if ($user == 'user') {
            $user = '';
        }
        if ($placement == 'placement') {
            $placement = '';
        }
        $publisherModel = new PublisherModel();
        $placementModel = new PlacementModel();
        //检测user和placement是否正确
        if (!empty($user)) {
            $onePublisher = $publisherModel->getOne([], ['id' => $user]);
            if (empty($onePublisher)) {
                echo "error publisher id";
                return;
            }
        }
        if (!empty($placement)) {
            $onePlacement = $placementModel->getOne([], ['uuid' => $placement]);
            if (empty($onePlacement)) {
                echo "error placement id";
                return;
            }
        }

        DealBlackRevenueService::toDealBlackRevenue($day, $user, $placement);
    }
}