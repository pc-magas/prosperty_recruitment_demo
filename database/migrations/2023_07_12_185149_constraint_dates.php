<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Sometimes if for the application is required a master-slave replication
 * it will be some lag between master and slave db.
 * 
 * Therefore despite checking via code the death date we also need to check upon write as well.
 * This is because laravel may check the death date via code. 
 */
return new class extends Migration
{

    public function up(): void
    {
        DB::statement("ALTER TABLE spies add constraint death_date_valid check ( spies.death_date IS NULL or spies.death_date > spies.birth_date )");
    }


    public function down(): void
    {
        DB::statement("ALTER TABLE spies drop constraint IF EXISTS death_date_valid");
    }
};
