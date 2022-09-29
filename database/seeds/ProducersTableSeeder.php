<?php

use Illuminate\Database\Seeder;

class ProducersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $producers = array('Apple', 'Samsung', 'Sony', 'OPPO', 'Huawei', 'Xiaomi');
        foreach ($producers as $producer) {
            DB::table('producers')->insert([
                'name' =>  $producer,
                'created_at'  =>  date('Y-m-d H:i:s', strtotime('now')),
                'updated_at'  =>  date('Y-m-d H:i:s', strtotime('now')),
            ]);
        }
    }
}
