<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\Admin;
use Carbon\Carbon;

class RandomClientSelectionReward extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RandomClientSelectionReward:monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Randomly Client Selection Reward at monthly basis';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = DB::table('admins')->select('id', 'role', 'first_name','last_name','email','phone')->where('role', 7)->inRandomOrder()->limit(1)->get();
        //dd($query);
        if(count($query) >0){
            $current_date = date('Y-m-d');
            $ins = DB::table('client_monthly_rewards')->insert(
                [
                    'reward_date' => $current_date,
                    'client_id' => $query[0]->id,
                    'role' => $query[0]->role,
                    'first_name' => $query[0]->first_name,
                    'last_name' => $query[0]->last_name,
                    'email' => $query[0]->email,
                    'phone' => $query[0]->phone,
                ]
            );
            if($ins){
                $this->info('Record is inserted.');
            } else {
                $this->info('Record is not inserted.');
            }
        } else {
            $this->info('No record is found.');
        }
    }
}
