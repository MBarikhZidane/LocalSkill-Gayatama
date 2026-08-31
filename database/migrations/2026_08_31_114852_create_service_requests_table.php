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
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->constrained('skill_categories')
                ->cascadeOnDelete();

            $table->string('title');
            $table->text('description');

            $table->decimal('budget_min', 12, 2)->nullable();
            $table->decimal('budget_max', 12, 2)->nullable();

            $table->date('deadline')->nullable();

            $table->string('location')->nullable();

            $table->enum('status', [
                'open',
                'in_progress',
                'completed',
                'cancelled',
            ])->default('open');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
