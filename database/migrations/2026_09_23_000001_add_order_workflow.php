<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check existing chat before any DDL; never guess how to merge conversations.
        foreach (['conservations' => ['id', 'order_id', 'created_at', 'updated_at'], 'messages' => ['id', 'conservation_id', 'sender_id', 'message', 'created_at', 'updated_at']] as $table => $columns) {
            if (Schema::hasTable($table) && ! Schema::hasColumns($table, $columns)) {
                throw new RuntimeException("Existing {$table} schema differs. Review the chat schema before installing this migration.");
            }
            if (Schema::hasTable($table)) {
                foreach (Schema::getColumns($table) as $column) {
                    if (! in_array($column['name'], $columns, true) && ! $column['nullable'] && $column['default'] === null && ! ($column['auto_increment'] ?? false)) {
                        throw new RuntimeException("Existing {$table}.{$column['name']} is required. Adapt writes before migrating.");
                    }
                }
            }
        }
        if (Schema::hasTable('conservations') && DB::table('conservations')->whereNotNull('order_id')->groupBy('order_id')->havingRaw('COUNT(*) > 1')->exists()) {
            throw new RuntimeException('Multiple conversations exist for an order. Resolve their history before installing; no data was changed.');
        }
        if (! Schema::hasTable('conservations')) {
            Schema::create('conservations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->nullable()->constrained('orders')->cascadeOnDelete();
                $table->timestamps();
            });
        }
        if (! Schema::hasIndex('conservations', ['order_id'], 'unique')) {
            Schema::table('conservations', fn (Blueprint $table) => $table->unique('order_id', 'workflow_conservation_order_unique'));
        }
        if (! Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('conservation_id')->constrained('conservations')->cascadeOnDelete();
                $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
                $table->text('message');
                $table->timestamps();
            });
        }
        foreach (['workflow_kind', 'workflow_from', 'workflow_to', 'workflow_token'] as $column) {
            if (! Schema::hasColumn('messages', $column)) {
                Schema::table('messages', fn (Blueprint $table) => $table->string($column, 64)->nullable());
            }
        }
        Schema::table('messages', fn (Blueprint $table) => $table->text('message')->change());
        if (! Schema::hasIndex('messages', 'workflow_message_token_unique')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->unique(['conservation_id', 'sender_id', 'workflow_token'], 'workflow_message_token_unique');
                $table->index(['conservation_id', 'id'], 'workflow_message_cursor_index');
            });
        }
        if (! Schema::hasTable('order_read_cursors')) {
            Schema::create('order_read_cursors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->unsignedBigInteger('message_id')->default(0);
                $table->unique(['order_id', 'user_id']);
            });
        }
        if (! Schema::hasTable('order_attachments')) {
            Schema::create('order_attachments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('message_id')->constrained('messages')->cascadeOnDelete();
                $table->string('path');
                $table->string('name');
                $table->string('mime', 100);
                $table->unsignedInteger('size');
            });
        }
        // Widen storage without rewriting or interpreting any historical value.
        Schema::table('orders', fn (Blueprint $table) => $table->string('status', 64)->default('pending')->change());
        if (! Schema::hasIndex('orders', 'workflow_provider_status_index')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->index(['provider_id', 'status'], 'workflow_provider_status_index');
                $table->index(['customer_id', 'status'], 'workflow_customer_status_index');
            });
        }
        if (! Schema::hasColumn('orders', 'brief')) {
            Schema::table('orders', fn (Blueprint $table) => $table->text('brief')->nullable());
        }
        if (! Schema::hasColumn('orders', 'scheduled_date')) {
            Schema::table('orders', fn (Blueprint $table) => $table->date('scheduled_date')->nullable());
        }
    }

    public function down(): void
    {
        throw new RuntimeException('Forward-only migration: rollback would discard conversations and new order states. Restore a verified backup or deploy a forward fix.');
    }
};
