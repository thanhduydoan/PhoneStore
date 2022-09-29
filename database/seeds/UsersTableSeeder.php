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
            'name' => 'Admin',
            'email' =>  'admin@gmail.com',
            'admin'    =>  1,
            'active'    =>  1,
            'password'  =>  bcrypt('admin@123'),
            'created_at'    =>  date('Y-m-d H:i:s', strtotime('now')),
            'updated_at'    =>  date('Y-m-d H:i:s', strtotime('now')),
        ]);
    }
}
