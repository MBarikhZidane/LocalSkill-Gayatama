<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckOrderWorkflow extends Command
{
    protected $signature = 'order-workflow:check';

    protected $description = 'Read-only compatibility check before installing the order workflow';

    public function handle(): int
    {
        $failed = false;
        foreach (['orders' => ['id', 'customer_id', 'provider_id', 'status', 'price', 'total_amount'], 'notifications' => ['id', 'type', 'notifiable_type', 'notifiable_id', 'data']] as $table => $columns) {
            if (! Schema::hasTable($table) || ! Schema::hasColumns($table, $columns)) {
                $this->error("Missing required {$table} schema.");
                $failed = true;
            }
        }
        foreach (['conservations' => ['id', 'order_id', 'created_at', 'updated_at'], 'messages' => ['id', 'conservation_id', 'sender_id', 'message', 'created_at', 'updated_at']] as $table => $columns) {
            if (! Schema::hasTable($table)) {
                $this->info("{$table}: absent; the migration will create it.");

                continue;
            }
            if (! Schema::hasColumns($table, $columns)) {
                $this->error("{$table}: incompatible schema; inspect column definitions before migrating.");
                $failed = true;

                continue;
            }
            foreach (Schema::getColumns($table) as $column) {
                if (! in_array($column['name'], $columns, true) && ! $column['nullable'] && $column['default'] === null && ! ($column['auto_increment'] ?? false)) {
                    $this->error("{$table}.{$column['name']}: extra required column; adapt writes before migrating.");
                    $failed = true;
                }
            }
            $this->info("{$table}: ".DB::table($table)->count().' existing rows (contents not read).');
        }
        if (Schema::hasTable('conservations') && Schema::hasColumn('conservations', 'order_id') && DB::table('conservations')->whereNotNull('order_id')->groupBy('order_id')->havingRaw('COUNT(*) > 1')->exists()) {
            $this->error('Duplicate order conversations exist. Preserve and reconcile their history first.');
            $failed = true;
        }
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'status')) {
            $this->table(['Existing status', 'Count'], DB::table('orders')->selectRaw('status, COUNT(*) as total')->groupBy('status')->get()->map(fn ($row) => [$row->status, $row->total])->all());
        }
        if ($failed) {
            $this->error('Compatibility check failed. No data was changed.');

            return self::FAILURE;
        }
        $this->info('Basic schema check passed. Test the migration on a database copy before production. No data was changed.');

        return self::SUCCESS;
    }
}
