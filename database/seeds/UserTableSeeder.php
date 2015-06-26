<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Security\User;
use Faker\Factory as Faker;

class UserTableSeeder extends Seeder {

    public function run()
    {
        //DB::table('users')->delete();

        User::create([
        	'name' => 'Noel',
        	'email' => 'noel.logan@gmail.com',
        	'password' => '123'
        ]);

        /*for ($i=0; $i < 30; $i++) { 
	        $faker=Faker::create();
	        User::create([
	        	'name'=>$faker->firstName,
	        	'email'=>$faker->unique()->email,
	        	'password'=>'123'
			]);
        }*/
    }

}