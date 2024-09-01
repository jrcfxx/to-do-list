<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TaskTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $timestamp = Carbon::now();
        
        DB::table('task')->insert([
            [
                'id' => 1,
                'user_id' => 1,
                'title' => Str::random(10),
                'description' => 'testing',
                'priority' => 5,
                'status' => 'open',
                'due_date' => Carbon::createFromFormat('d/m/Y', '15/07/2024')->format('Y-m-d'),
                'completeness_date' => Carbon::createFromFormat('d/m/Y', '10/07/2024')->format('Y-m-d'),
                'delete_date' => Carbon::createFromFormat('d/m/Y', '15/07/2024')->format('Y-m-d'),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]
        ]);
    }
}
