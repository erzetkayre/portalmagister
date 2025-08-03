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
        Schema::create('tesis_sempro_elektro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tesis_id')->constrained('tesis_elektro')->cascadeOnDelete();
            $table->date('tanggal')->nullable();
            $table->foreignId('tempat')->constrained('ref_ruang')->cascadeOnDelete();
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->enum('status_seminar', ['waiting', 'approved', 'rejected', 'revision', 'done'])->default('waiting');
            $table->string('draft_sempro')->nullable();
            $table->text('summary')->nullable();
            $table->string('berita_acara')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tesis_sempro_elektro');
    }
};
