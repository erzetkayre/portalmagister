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
        Schema::create('tesis_ujian_proposal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tesis_id')->constrained('tesis_draft')->cascadeOnDelete();
            $table->date('tanggal')->nullable();
            $table->string('tempat')->nullable();
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->string('kartu_bimbingan')->nullable();
            $table->string('surat_kelayakan')->nullable();
            $table->enum('status_seminar', ['kartu_uploadedw','waiting', 'approved', 'rejected', 'revision', 'done'])->default('waiting');
            $table->string('draft_semhas')->nullable();
            $table->text('summary')->nullable();
            $table->text('catatan')->nullable();
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
        Schema::dropIfExists('tesis_ujian');
    }
};
