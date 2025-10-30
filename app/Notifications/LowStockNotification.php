<?php

namespace App\Notifications;

use App\Models\MenuItem;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    public function __construct(
        public MenuItem $menuItem,
        public int $remainingStock
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Peringatan Stok Menu Rendah')
            ->line("Stok menu {$this->menuItem->name} menipis.")
            ->line("Sisa stok: {$this->remainingStock}")
            ->line("Batas stok: {$this->menuItem->low_stock_threshold}")
            ->action('Kelola Menu', route('admin.menu-items.edit', $this->menuItem));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'menu_item_id' => $this->menuItem->id,
            'menu_name' => $this->menuItem->name,
            'remaining_stock' => $this->remainingStock,
            'threshold' => $this->menuItem->low_stock_threshold,
            'message' => "Stok {$this->menuItem->name} tersisa {$this->remainingStock} (batas {$this->menuItem->low_stock_threshold}).",
            'url' => route('admin.menu-items.edit', $this->menuItem),
        ];
    }
}

