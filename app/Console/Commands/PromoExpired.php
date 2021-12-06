<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Promo;
use Carbon\Carbon;

class PromoExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promo:expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired promo';

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
        // \Log::info("promo:expired is working");
        
        $promo = Promo::where('expired_at', '<' , Carbon::now())->get();
        foreach ($promo as $i) {
            $i->delete();
        }
        $this->info('Promo has been deleted');
    }
}
