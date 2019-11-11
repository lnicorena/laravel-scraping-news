<?php

use Illuminate\Database\Seeder;

class SourcesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sources')->insert([
            'name' => 'TechCrunch',
            'slug' => 'tech-crunch',
            'active' => 1
        ]);
    }
}
