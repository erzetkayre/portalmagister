<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('pwk')->create('tesis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mhs_id')->constrained('ref_mahasiswa')->onDelete('cascade');
            $table->string('judul');
            $table->text('abstrak')->nullable();
            $table->enum('status', ['draft', 'menunggu', 'setuju', 'tolak'])->default('draft');
            $table->enum('proses_tesis', ['pratesis', 'tesis_1', 'tesis_2'])->default('pratesis');
            $table->timestamp('tgl_pengajuan')->nullable();
            $table->timestamp('tgl_setuju')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::connection('pwk')->create('tesis_pengajuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tesis_id')->constrained('tesis')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('ref_dosen');
            $table->tinyInteger('slot'); // 1 = pembimbing 1, 2 = pembimbing 2
            $table->text('alasan')->nullable();
            $table->timestamps();
        });

        Schema::connection('pwk')->create('tesis_pembimbing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tesis_id')->constrained('tesis')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('ref_dosen');
            $table->tinyInteger('slot'); // 1 = pembimbing 1, 2 = pembimbing 2
            $table->timestamps();
        });

        Schema::connection('pwk')->create('tesis_logbook', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tesis_id')->constrained('tesis')->onDelete('cascade');
            $table->enum('proses_tesis', ['pratesis', 'tesis_1', 'tesis_2']);
            $table->tinyInteger('nomor_konsultasi');
            $table->date('tgl_konsultasi');
            $table->text('ringkasan');
            $table->string('file_konsultasi')->nullable();
            $table->timestamps();
        });

        Schema::connection('pwk')->create('tesis_dokumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tesis_id')->unique()->constrained('tesis')->onDelete('cascade');

            // Registration documents (step 2)
            $table->string('file_khs')->nullable();
            $table->string('file_krs')->nullable();
            $table->string('file_surat_permohonan')->nullable();

            // Coordinator-generated on approval
            $table->string('file_sk_pembimbing')->nullable();

            // Pratesis exam documents
            $table->string('file_surat_kelayakan_pratesis')->nullable();
            $table->string('file_revisi_pratesis')->nullable();
            $table->string('file_laporan_pratesis')->nullable();

            // Tesis 1 exam documents
            $table->string('file_surat_kelayakan_tesis_1')->nullable();
            $table->string('file_revisi_tesis_1')->nullable();
            $table->string('file_laporan_tesis_1')->nullable();

            // Tesis 2 exam documents
            $table->string('file_surat_kelayakan_tesis_2')->nullable();
            $table->string('file_revisi_tesis_2')->nullable();
            $table->string('file_laporan_tesis_2')->nullable();

            $table->timestamps();
        });

        Schema::connection('pwk')->create('ujian_tesis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tesis_id')->constrained('tesis')->onDelete('cascade');
            $table->enum('jenis_ujian', ['pratesis', 'tesis_1', 'tesis_2']);

            // Student submission
            $table->string('draft_link')->nullable();

            // Coordinator scheduling
            $table->foreignId('ruang_id')->nullable()->constrained('ref_ruang')->nullOnDelete();
            $table->date('tanggal')->nullable();
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->foreignId('penguji_1_id')->nullable()->constrained('ref_dosen')->nullOnDelete();
            $table->foreignId('penguji_2_id')->nullable()->constrained('ref_dosen')->nullOnDelete();

            // Status and result
            $table->enum('status_jadwal', ['menunggu', 'dijadwalkan', 'selesai'])->default('menunggu');
            $table->enum('hasil', ['lulus', 'lulus_bersyarat', 'lulus_revisi', 'tidak_lulus'])->nullable();

            $table->unique(['tesis_id', 'jenis_ujian']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('pwk')->dropIfExists('ujian_tesis');
        Schema::connection('pwk')->dropIfExists('tesis_dokumen');
        Schema::connection('pwk')->dropIfExists('tesis_logbook');
        Schema::connection('pwk')->dropIfExists('tesis_pembimbing');
        Schema::connection('pwk')->dropIfExists('tesis_pengajuan');
        Schema::connection('pwk')->dropIfExists('tesis');
    }
};
