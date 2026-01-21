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
        Schema::create('tesis_logbook_proposal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tesis_id')->constrained('tesis_draft')->cascadeOnDelete();
            $table->enum('tahap', ['pratesis', 'tesis_1', 'tesis_2'])->default('pratesis');
            $table->text('kegiatan');
            $table->integer('bab')->nullable();
            $table->text('kendala')->nullable();
            $table->text('rencana')->nullable();
            $table->string('file_scan_bimbingan')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tesis_logbook');
    }
};
