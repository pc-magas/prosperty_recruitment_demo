<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('spies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('surname');
            $table->string('agency')->default('NO-AGENCY');
            $table->string('country_of_operation')->nullable();
            $table->date('birth_date');
            $table->date('death_date')->nullable();

            $table->unique([
                'name',
                'surname',
                'agency',
                'birth_date'
            ],'unique_spy');

            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spies');
    }
};
