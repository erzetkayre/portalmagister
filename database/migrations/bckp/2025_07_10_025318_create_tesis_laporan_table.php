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
        Schema::create('tesis_laporan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tesis_id')->constrained('tesis')->cascadeOnDelete();
            $table->string('rev_pratesis')->nullable();
            $table->string('rev_tesis_satu')->nullable();
            $table->string('rev_tesis_dua')->nullable();
            $table->string('final_pratesis')->nullable();
            $table->string('final_tesis_satu')->nullable();
            $table->string('final_tesis_dua')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tesis_laporan');
    }
};
