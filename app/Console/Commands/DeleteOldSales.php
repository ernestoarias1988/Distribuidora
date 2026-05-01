<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class DeleteOldSales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
 protected $signature = 'sales:delete-old';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete sales older than a specified time';

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
     * @return int
     */
    public function handle()
    {
        $thresholdDate = Carbon::now()->subMonths(10); // Adjust as needed (e.g., delete sales older than 6 months)

        $deleted = DB::table('ventas')
            ->where('created_at', '<', $thresholdDate)
            ->delete();

        $this->info("$deleted old sales records deleted.");

        $deleted = DB::table('productos_vendidos')
            ->where('created_at', '<', $thresholdDate)
            ->delete();

        $this->info("$deleted old sales records deleted.");


        return 0;
    }
}



