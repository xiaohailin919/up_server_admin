<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\MySql\ReportImport as ReportImportMyModel;
use App\Models\MySql\ReportUnit as ReportUnitMyModel;
use App\Imports;

/**
 * Class AutoReportImport
 * @package App\Console\Commands
 * @deprecated
 */
class AutoReportImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:report_import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deprecated!!! Command description';

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
        $reportImportMyModel = new ReportImportMyModel();
        $first = $reportImportMyModel->where('status', ReportImportMyModel::STATUS_PENDING)
            ->orderBy('create_time')
            ->first();
        if(empty($first)){
            return true;
        }
        $first = $first->toArray();
        echo "Start {$first['file_name']}\n";
        // 更新为正在执行
        $reportImportMyModel->where('id', $first['id'])
            ->update([
                'status' => ReportImportMyModel::STATUS_IMPORTING
            ]);
        $path = env('REPORT_IMPORT_PATH') ? env('REPORT_IMPORT_PATH') : storage_path('app') . '/' . $first['file_path'];
        $collection = null;
        switch($first['firm_id']){
            case 8:
                $collection = new Imports\TencentReportImport();
                break;
            case 15:
                $collection = new Imports\ToutiaoReportImport();
                break;
            case 22:
                $collection = new Imports\BaiduReportImport();
                break;
            default:
                return true;
                break;
        }
        $collection->setSetting($first);
        try {
            Excel::import($collection, $path);
            $reportImportMyModel->where('id', $first['id'])
                ->update([
                    'import_time' => date('Y-m-d H:i:s'),
                    'status' => ReportImportMyModel::STATUS_SUCCESS
                ]);
            echo('Success: ' . $collection->getCount() . "\n");
        } catch (\Exception $e){
            $reportImportMyModel->where('id', $first['id'])
                ->update([
                    'import_time' => date('Y-m-d H:i:s'),
                    'status' => ReportImportMyModel::STATUS_FAILED
                ]);
            echo("Failed.\n");
        }
    }
}
