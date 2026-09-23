<?php

namespace App\Services;

use App\Models\Conservation;
use App\Models\Message;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderWorkflow
{
    public const TRANSITIONS = [
        'accept' => ['pending', 'provider_id', 'in_progress'],
        'decline' => ['pending', 'provider_id', 'declined'],
        'submit' => ['in_progress', 'provider_id', 'submitted'],
        'confirm' => ['submitted', 'customer_id', 'completed'],
        'issues' => ['submitted', 'customer_id', 'issue_reported'],
        'revision' => ['issue_reported', 'provider_id', 'in_progress'],
        'cancel' => ['pending', 'customer_id', 'cancelled'],
    ];

    public static function authorize(Order $order, int $userId): void
    {
        abort_unless(in_array($userId, [(int) $order->customer_id, (int) $order->provider_id], true), 403);
    }

    public static function writable(Order $order): bool
    {
        return in_array($order->status, ['pending', 'in_progress', 'submitted', 'issue_reported'], true);
    }

    public function write(Order $order, int $userId, string $action, array $data, array $files = []): Message
    {
        self::authorize($order, $userId);
        $paths = [];
        try {
            return DB::transaction(function () use ($order, $userId, $action, $data, $files, &$paths) {
                $locked = Order::query()->lockForUpdate()->findOrFail($order->id);
                self::authorize($locked, $userId);
                $conversation = Conservation::firstOrCreate(['order_id' => $locked->id]);
                $body = trim($data['body'] ?? '');
                if ($action === 'message') {
                    $existing = $conversation->messages()->where('sender_id', $userId)->where('workflow_token', $data['token'])->first();
                    if ($existing) {
                        return $existing;
                    }
                    abort_unless(self::writable($locked), 409, 'This conversation is read-only.');
                    if ($body === '' && count($files) === 0) {
                        throw ValidationException::withMessages(['body' => 'Write a message or attach a file.']);
                    }
                    $from = $to = null;
                } else {
                    abort_unless(isset(self::TRANSITIONS[$action]), 404);
                    [$from, $actor, $to] = self::TRANSITIONS[$action];
                    abort_unless((int) $locked->{$actor} === $userId, 403);
                    abort_unless($locked->status === $from, 409, 'The order status changed. Review the current status before trying again.');
                    if (in_array($action, ['submit', 'issues'], true) && $body === '') {
                        throw ValidationException::withMessages(['body' => 'A completion note or issue reason is required.']);
                    }
                    $changes = ['status' => $to, 'updated_at' => now()];
                    if ($action === 'accept') {
                        $changes['started_at'] = now();
                    }
                    if ($action === 'confirm') {
                        $changes['completed_at'] = now();
                    }
                    // Compare-and-update also protects engines without row-level SELECT locks.
                    abort_unless(Order::whereKey($locked->id)->where('status', $from)->update($changes) === 1, 409, 'The order status changed. Refresh and try again.');
                }
                $message = $conversation->messages()->create([
                    'sender_id' => $userId, 'message' => $body,
                    'workflow_kind' => $action === 'message' ? null : $action,
                    'workflow_from' => $from, 'workflow_to' => $to,
                    'workflow_token' => $action === 'message' ? $data['token'] : null,
                ]);
                foreach ($files as $file) {
                    $path = $file->store('orders/'.$locked->id, 'order_private');
                    if (! $path) {
                        throw new \RuntimeException('Attachment could not be saved.');
                    }
                    $paths[] = $path;
                    $name = Str::limit(preg_replace('/[^\pL\pN ._-]/u', '_', basename(str_replace('\\', '/', $file->getClientOriginalName()))), 180, '');
                    DB::table('order_attachments')->insert(['message_id' => $message->id, 'path' => $path, 'name' => $name ?: 'attachment', 'mime' => $file->getMimeType(), 'size' => $file->getSize()]);
                }
                $recipient = $userId === (int) $locked->customer_id ? $locked->provider_id : $locked->customer_id;
                if ((int) $recipient !== $userId) {
                    DB::table('notifications')->insert([
                        'id' => (string) Str::uuid(), 'type' => self::class,
                        'notifiable_type' => (new User)->getMorphClass(), 'notifiable_id' => $recipient,
                        'data' => json_encode(['order_id' => $locked->id, 'order_number' => $locked->order_number, 'message_id' => $message->id, 'type' => $action, 'title' => $action === 'message' ? 'New order message' : 'Order updated', 'message' => $action === 'message' ? 'A new message on #'.$locked->order_number : '#'.$locked->order_number.': '.Order::statusLabel($to)]),
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                }

                return $message;
            });
        } catch (\Throwable $exception) {
            Storage::disk('order_private')->delete($paths);
            throw $exception;
        }
    }

    public static function unread(int $userId, ?array $ids = null): array
    {
        return DB::table('messages as m')->join('conservations as c', 'c.id', '=', 'm.conservation_id')
            ->join('orders as o', 'o.id', '=', 'c.order_id')
            ->leftJoin('order_read_cursors as r', function ($join) use ($userId) {
                $join->on('r.order_id', '=', 'o.id')->where('r.user_id', $userId);
            })
            ->where(fn ($q) => $q->where('o.customer_id', $userId)->orWhere('o.provider_id', $userId))
            ->when($ids !== null, fn ($q) => $q->whereIn('o.id', $ids))
            ->whereNull('m.workflow_kind')->where('m.sender_id', '<>', $userId)
            ->whereRaw('m.id > COALESCE(r.message_id, 0)')
            ->groupBy('o.id')->selectRaw('o.id, COUNT(*) as unread')->pluck('unread', 'id')->all();
    }
}
