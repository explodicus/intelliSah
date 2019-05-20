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

        DB::table('users')->insert([
            'email' => 'bot1@sah.local',
            'name' => 'BOT Aurel',
            'password' => bcrypt('123456'),
        ]);

        DB::table('users')->insert([
            'email' => 'bot2@sah.local',
            'name' => 'BOT Calin',
            'password' => bcrypt('123456'),
        ]);

        DB::table('users')->insert([
            'email' => 'bot3@sah.local',
            'name' => 'BOT Florica',
            'password' => bcrypt('123456'),
        ]);

        DB::table('users')->insert([
            'email' => 'bot4@sah.local',
            'name' => 'BOT Galina',
            'password' => bcrypt('123456'),
        ]);

        DB::table('users')->insert([
            'email' => 'bot5@sah.local',
            'name' => 'BOT Mugur',
            'password' => bcrypt('123456'),
        ]);

        DB::table('users')->insert([
            'email' => 'bot6@sah.local',
            'name' => 'BOT Norocel',
            'password' => bcrypt('123456'),
        ]);

        DB::table('users')->insert([
            'email' => 'bot7@sah.local',
            'name' => 'BOT Petru',
            'password' => bcrypt('123456'),
        ]);

        DB::table('users')->insert([
            'email' => 'bot8@sah.local',
            'name' => 'BOT Vadim',
            'password' => bcrypt('123456'),
        ]);

        DB::table('users')->insert([
            'email' => 'bot9@sah.local',
            'name' => 'BOT Gabi',
            'password' => bcrypt('123456'),
        ]);
    }
}
