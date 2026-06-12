<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('pwk')->create('tesis_perubahan_judul', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tesis_id')->constrained('tesis')->onDelete('cascade');
            $table->string('judul_lama');
            $table->string('judul_baru');
            $table->text('alasan');
            $table->enum('status', ['menunggu', 'setuju', 'tolak'])->default('menunggu');
            $table->timestamp('tgl_pengajuan')->nullable();
            $table->timestamp('tgl_diproses')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('pwk')->dropIfExists('tesis_perubahan_judul');
    }
};
