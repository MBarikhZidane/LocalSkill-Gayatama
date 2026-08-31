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
        Schema::create('study_programs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('university_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('code')->nullable();

            $table->timestamps();

            $table->unique(['university_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_programs');
    }
};
