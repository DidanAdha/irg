<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Schedule;
use App\Restaurant as Resto;
use Carbon\Carbon;

class ScheduleExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired schedule';

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
        // \Log::info("schedule:expired is working");

        $schedule = Schedule::where('expired_at', '<' , Carbon::now())->get();
        foreach ($schedule as $i) {
            $resto = Resto::select('id', 'scheduled')->find($i->restaurants_id);
            $resto->scheduled = 0;
            $resto->save();

            $i->delete();
        }
    }
}
