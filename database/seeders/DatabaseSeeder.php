<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /**
         * Seeder dues to constraint may cause error.
         * But for seeding we do not care
         * because we just want some data
         */
        try{

            \App\Models\User::factory()->create([
                'name' => 'Test User',
                'email' => 'test1@example.com',
                'password' => Hash::make('1234')
            ]);
        } catch(\Exception $e) {

        }

        \App\Models\Spy::factory()->count(20)->create();
    }
}
