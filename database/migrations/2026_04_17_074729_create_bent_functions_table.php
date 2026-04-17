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
        Schema::create('bent_functions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Nomsiz funksiya');
            $table->text('truth_table');
            $table->integer('n');
            $table->boolean('is_bent')->default(false);
            $table->integer('nonlinearity')->default(0);
            $table->integer('alg_degree')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bent_functions');
    }
};
