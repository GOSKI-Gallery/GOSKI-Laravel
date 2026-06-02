<?php

namespace App\Http\Controllers;

use App\Services\SupabaseNotificationService;

class NotificationsController extends Controller
{
    protected $notificationService;

    public function __construct(SupabaseNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $notifications = $this->notificationService->getNotifications();

        return response()->json($notifications);
    }

    public function markAsRead()
    {
        $this->notificationService->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notificações marcadas como lidas.',
        ]);
    }

    public function delete($id)
    {
        $this->notificationService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Notificação removida.',
        ]);
    }
}
