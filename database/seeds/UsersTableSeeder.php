<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'email' => 'admin@sah.local',
            'name' => 'admin',
            'password' => bcrypt('123456'),
        ]);

        DB::table('users')->insert([
            'email' => 'admin1@sah.local',
            'name' => 'admin1',
            'password' => bcrypt('123456'),
        ]);

        DB::table('users')->insert([
            'email' => 'admin2@sah.local',
            'name' => 'admin2',
            'password' => bcrypt('123456'),
        ]);

        DB::table('users')->insert([
            'email' => 'admi3@sah.local',
            'name' => 'admin3',
            'password' => bcrypt('123456'),
        ]);
    }
}
