<?php

namespace App\Services\Admin;

use App\Models\Notification;
use App\Models\User;
use App\Services\BaseService;
use Carbon\Carbon;

class NotificationManagementService extends BaseService
{
    public function getNotificationProfile(Notification $notification, User $user): array
    {
        $allNotifications = $user->notifications()->get();

        $recentByType = $user->notifications()
            ->where('type', $notification->type)
            ->latest('created_at')
            ->limit(8)
            ->get()
            ->map(fn (Notification $item) => [
                'id' => $item->id,
                'title' => $item->title,
                'date' => optional($item->created_at)->format('Y-m-d H:i'),
                'is_read' => $item->is_read,
            ])
            ->values()
            ->all();

        $relatedData = collect($notification->data ?? [])->map(function ($value, $key) {
            $arabicKey = match ($key) {
                'teacher_id' => 'المعلم',
                'student_id' => 'الطالب',
                'group_id' => 'الحلقة',
                'amount' => 'المبلغ',
                'date' => 'التاريخ',
                default => str_replace('_', ' ', $key),
            };

            return [
                'key' => $arabicKey,
                'value' => is_scalar($value) ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE),
            ];
        })->values()->all();

        $stats = [
            'total' => $allNotifications->count(),
            'unread' => $allNotifications->where('is_read', false)->count(),
            'read' => $allNotifications->where('is_read', true)->count(),
            'same_type_count' => $allNotifications->where('type', $notification->type)->count(),
            'today_count' => $allNotifications->filter(fn ($item) => optional($item->created_at)?->isToday())->count(),
        ];

        $typeLabel = match ($notification->type) {
            'absence' => 'غياب',
            'delay' => 'تأخير',
            'report' => 'تقرير',
            'financial' => 'مالي',
            default => $notification->type,
        };

        return [
            'notification' => [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'type' => $notification->type,
                'type_label' => $typeLabel,
                'type_color' => $notification->type_color,
                'type_icon' => $notification->type_icon,
                'is_read' => $notification->is_read,
                'created_at' => optional($notification->created_at)->format('Y-m-d H:i'),
            ],
            'stats' => $stats,
            'related_data' => $relatedData,
            'recent_same_type' => $recentByType,
            'generated_at' => Carbon::now()->format('Y-m-d H:i'),
        ];
    }
}

