<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Order;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification
{
   use Queueable;

    public $order;
    public $type; // 'new_order', 'accepted', 'completed', 'cancelled'

    public function __construct(Order $order, string $type)
    {
        $this->order = $order;
        $this->type = $type;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $serviceTitle = $this->order->service->title ?? 'Custom Service Request';
        $orderNumber = $this->order->order_number;

        switch ($this->type) {
            case 'new_order':
                $title = 'Pesanan Baru Masuk!';
                $message = "Anda mendapatkan pesanan baru #{$orderNumber} untuk layanan '{$serviceTitle}'.";
                break;
            case 'accepted':
                $title = 'Pesanan Diterima';
                $message = "Pesanan #{$orderNumber} ('{$serviceTitle}') telah diterima oleh penyedia jasa.";
                break;
            case 'completed':
                $title = 'Pesanan Selesai';
                $message = "Pesanan #{$orderNumber} ('{$serviceTitle}') telah ditandai selesai.";
                break;
            case 'cancelled':
                $title = 'Pesanan Dibatalkan';
                $message = "Pesanan #{$orderNumber} ('{$serviceTitle}') telah dibatalkan.";
                break;
            default:
                $title = 'Pembaruan Pesanan';
                $message = "Status pesanan #{$orderNumber} telah diperbarui.";
                break;
        }

        return [
            'order_id' => $this->order->id,
            'order_number' => $orderNumber,
            'title' => $title,
            'message' => $message,
            'type' => $this->type,
        ];
    }
}
