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
        Schema::create('tesis_pembimbing_elektro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tesis_id')->constrained('tesis_elektro')->cascadeOnDelete();
            $table->foreignId('dospem')->constrained('ref_dosen');
            $table->enum('no_pembimbing', [1, 2]);
            $table->enum('status_tesis', ['pending', 'waiting', 'approved', 'rejected'])->default('waiting');
            $table->enum('status_sempro', ['pending', 'waiting', 'approved', 'rejected'])->default('waiting');
            $table->enum('status_semhas', ['pending', 'waiting', 'approved', 'rejected'])->default('waiting');
            $table->enum('status_ujian', ['pending', 'waiting', 'approved', 'rejected'])->default('waiting');
            $table->float('nilai_sempro') ->nullable();
            $table->float('nilai_semhas') ->nullable();
            $table->float('nilai_ujian') ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tesis_pembimbing_elektro');
    }
};
