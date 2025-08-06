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
        Schema::create('tesis_penguji_elektro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tesis_id')->constrained('tesis_elektro')->cascadeOnDelete();
            $table->foreignId('penguji_id')->constrained('ref_dosen');
            $table->enum('no_penguji', [1, 2]);
            $table->float('nilai_sempro') ->nullable();
            $table->float('nilai_semhas') ->nullable();
            $table->float('nilai_ujian') ->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tesis_penguji_elektro');
    }
};
