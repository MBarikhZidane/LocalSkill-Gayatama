<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('professional_title')->nullable();
        });
        Schema::table('portofolios', function (Blueprint $table): void {
            $table->string('title')->nullable();
            $table->string('project_type')->nullable();
            $table->text('description')->nullable();
        });
        Schema::table('orders', function (Blueprint $table): void {
            $table->date('scheduled_date')->nullable();
            $table->text('brief')->nullable();
        });
        Schema::table('services', function (Blueprint $table): void {
            $table->text('moderation_note')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('professional_title'));
        Schema::table('portofolios', fn (Blueprint $table) => $table->dropColumn(['title', 'project_type', 'description']));
        Schema::table('orders', fn (Blueprint $table) => $table->dropColumn(['scheduled_date', 'brief']));
        Schema::table('services', fn (Blueprint $table) => $table->dropColumn('moderation_note'));
    }
};
