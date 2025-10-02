<?php
  
use Illuminate\Database\Seeder;
use App\Models\Admin;
   
class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::create([
            'first_name' 	=> 'Admin',
            'last_name' 	=> '..',
            'email' 		=> 'admin@gmail.com',
            'password' 		=> Hash::make('bansal'),
            'phone' 		=> str_replace("-","", '477002454'),
            'company_name' 	=> 'Bansal Education Group',        
            'role' 	=> 1,        
        ]);
    }
}