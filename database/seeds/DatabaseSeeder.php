<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        DB::table('users')->insert([
            'is_admin' => 1
            , 'name' => 'Manash'
            , 'email' => 'manash.pstu@gmail.com'
            , 'password' => bcrypt('secret')
        ]);
        factory(App\User::class, 50)->create()->each(function ($user){
            for($i = 0; $i < 5; $i++){
                $user->posts()->save(factory(App\Post::class)->create());
            }            
        });
    }
}
