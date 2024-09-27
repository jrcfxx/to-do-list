<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $timestamp = Carbon::now();
        
        DB::table('users')->insert([
        [
            'name' => Str::random(10),
            'email' => Str::random(10).'@example.com',
            'password' => Hash::make('password'),
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ],
        [
            'name' => Str::random(10),
            'email' => Str::random(10).'@example.com',
            'password' => Hash::make('password'),
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ],
        [
            'name' => Str::random(10),
            'email' => Str::random(10).'@example.com',
            'password' => Hash::make('password'),
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ],
        [
            'name' => "test",
            'email' => "test@gmail.com",
            'password' => "test",
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ],
        ]);
    }
}

