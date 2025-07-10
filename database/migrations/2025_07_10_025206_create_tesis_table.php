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
        Schema::create('tesis_draft', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mhs_id')->constrained('ref_mahasiswa')->cascadeOnDelete();
            $table->text('us_judul');
            $table->text('us_abstrak')->nullable();
            $table->foreignId('us_dospem_satu')->constrained('ref_dosen')->cascadeOnDelete();
            $table->foreignId('us_dospem_dua')->constrained('ref_dosen')->cascadeOnDelete();
            $table->text('ket_dospem_satu')->nullable();
            $table->text('ket_dospem_dua')->nullable();
            $table->timestamp('tgl_pengajuan')->nullable();
            $table->string('khs')->nullable();
            $table->string('file_krs')->nullable();
            $table->string('file_sk_pembimbing')->nullable();
            $table->string('file_tesis')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('tesis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mhs_id')->constrained('ref_mahasiswa')->cascadeOnDelete();
            $table->text('judul');
            $table->text('abstrak')->nullable();
            $table->timestamp('tgl_mulai')->nullable();
            $table->timestamp('tgl_selesai')->nullable();
            $table->enum('status_pratesis', ['pending', 'waiting', 'approved', 'rejected'])->default('waiting');
            $table->enum('status_tesis_satu', ['pending', 'waiting', 'approved', 'rejected'])->default('waiting');
            $table->enum('status_tesis_dua', ['pending', 'waiting', 'approved', 'rejected'])->default('waiting');
            $table->string('file_tesis')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tesis');
        Schema::dropIfExists('tesis_draft');
    }
};
