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
        Schema::create('tesis_elektro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('ref_mahasiswa')->onDelete('cascade');
            $table->text('judul');
            $table->text('abstrak')->nullable();
            $table->timestamp('tgl_mulai')->nullable();
            $table->timestamp('tgl_selesai')->nullable();
            $table->enum('status_proposal_tesis', ['pending', 'waiting', 'approved', 'rejected'])->default('waiting');
            $table->enum('status_sempro', ['pending', 'waiting', 'approved', 'rejected'])->default('waiting');
            $table->enum('status_semhas', ['pending', 'waiting', 'approved', 'rejected'])->default('waiting');
            $table->enum('status_ujian', ['pending', 'waiting', 'approved', 'rejected'])->default('waiting');
            $table->string('file_tesis')->nullable();
            $table->integer('proses_tesis')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tesis_elektro');
    }
};
