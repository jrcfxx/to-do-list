<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TaskChangeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $timestamp = Carbon::now();
        
        DB::table('task_change')->insert([
            [
                'id' => 1,
                'task_id' => 1,
                'changed_field' => 'testing',
                'old_value' => 'testing',
                'new_value' => 'testing',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]
        ]);
    }
}
