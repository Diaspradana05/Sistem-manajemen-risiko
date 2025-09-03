<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationDropdown extends Component
{
    public function markAsRead($notificationId)
    {
        $notif = Auth::user()->notifications()->where('id', $notificationId)->first();
        if ($notif) {
            $notif->markAsRead();
        }
    }

    public function render()
    {
        return view('livewire.notification-dropdown', [
            'notifications' => Auth::user()->notifications()->latest()->take(5)->get(),
            'unreadCount' => Auth::user()->unreadNotifications()->count(),
        ]);
    }
}
