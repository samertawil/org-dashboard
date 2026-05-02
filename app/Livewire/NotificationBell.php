<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class NotificationBell extends Component
{
    /** Poll every 30s to refresh count */
    public function getListeners(): array
    {
        return [];
    }

    #[Computed]
    public function unreadCount(): int
    {
        return Auth::user()->unreadNotifications()->count();
    }

    #[Computed]
    public function notifications()
    {
        return Auth::user()
            ->notifications()
            ->latest()
            ->take(15)
            ->get();
    }

    public function markAllRead(): void
    {
        Auth::user()->unreadNotifications->markAsRead();
        unset($this->unreadCount, $this->notifications);
    }

    public function markRead(string $notificationId): void
    {
        $notification = Auth::user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            unset($this->unreadCount, $this->notifications);
        }
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
