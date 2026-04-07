<?php

namespace App\Http\Controllers\Admin;

use App\Models\Notification;
use App\Services\Admin\NotificationManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminNotificationController extends AdminController
{
    protected string $title = 'الإشعارات';

    public function __construct(
        private readonly NotificationManagementService $notificationManagementService
    ) {}

    public function index(): View
    {
        $user = auth()->user();

        // المشرف العام يرى كل الإشعارات، غيره يرى إشعارات فرعه فقط
        $notificationsQuery = $user->notifications();

        if (! $user->isSuperAdmin() && $user->branch_id) {
            $notificationsQuery = \App\Models\Notification::query()
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
        }

        $unreadCount = (clone $notificationsQuery)
            ->where('is_read', false)
            ->count();

        $notifications = (clone $notificationsQuery)
            ->orderByDesc('created_at')
            ->paginate(20);

        return $this->adminView('admin.notifications.index', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'الإشعارات'],
            ],
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount,
        ]);
    }

    public function show(Notification $notification): View
    {
        abort_if($notification->user_id !== auth()->id(), 404);

        if (! $notification->is_read) {
            $notification->update(['is_read' => true]);
        }

        $profile = $this->notificationManagementService->getNotificationProfile($notification->fresh(), auth()->user());

        return $this->adminView('admin.notifications.show', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'الإشعارات', 'url' => route('admin.notifications.index')],
                ['title' => 'تفاصيل الإشعار'],
            ],
            'notification' => $notification,
            'profile' => $profile,
        ]);
    }

    public function markAsRead(Notification $notification): RedirectResponse
    {
        abort_if($notification->user_id !== auth()->id(), 404);

        $notification->update(['is_read' => true]);

        return back()->with('success', 'تم تحديث الإشعار.');
    }

    public function markAllAsRead(): RedirectResponse
    {
        auth()->user()->notifications()
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'تم تحديث جميع الإشعارات.');
    }

    public function unreadCount(): JsonResponse
    {
        $count = auth()->user()->notifications()
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }}

