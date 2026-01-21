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
        Schema::create('tesis_pembimbing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tesis_id')->constrained('tesis')->cascadeOnDelete();
            $table->foreignId('dospem')->constrained('ref_dosen');
            $table->enum('no_pembimbing', [1, 2]);
            $table->enum('status_pratesis', ['waiting', 'approved', 'rejected'])->default('waiting');
            $table->enum('status_tesis_satu', ['waiting', 'approved', 'rejected'])->default('waiting');
            $table->enum('status_tesis_dua', ['waiting', 'approved', 'rejected'])->default('waiting');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tesis_pembimbing');
    }
};
