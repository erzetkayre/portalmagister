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
        Schema::create('tesis_dokumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tesis_id')->constrained('tesis')->cascadeOnDelete();
            $table->string('file_sk_pembimbing')->nullable();
            $table->string('file_perm_pratesis')->nullable();
            $table->string('file_perm_tesis')->nullable();
            $table->string('file_perm_tesis_dua')->nullable();
            $table->string('file_turnitin')->nullable();
            $table->string('file_logbook')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tesis_dokumen');
    }
};
