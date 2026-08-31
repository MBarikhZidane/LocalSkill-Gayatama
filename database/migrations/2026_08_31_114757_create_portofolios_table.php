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
        Schema::create('portofolios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('skill_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('type', [
                'certificate',
                'portfolio',
                'assessment',
                'endorsement',
            ]);

            $table->string('evidence')->nullable();

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
            ])->default('pending');

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portofolios');
    }
};
