<?php

namespace App\Enums;

/**
 * UserStatusEnum — حالات المستخدم
 * تشمل: نشط / غير نشط / موقوف
 */
enum UserStatusEnum: string
{
    case Active    = 'active';
    case Inactive  = 'inactive';
    case Suspended = 'suspended';

    /**
     * التسمية العربية
     */
    public function label(): string
    {
        return match($this) {
            self::Active    => 'نشط',
            self::Inactive  => 'غير نشط',
            self::Suspended => 'موقوف',
        };
    }

    /**
     * CSS class للـ badge
     */
    public function badgeClass(): string
    {
        return match($this) {
            self::Active    => 'bg-success',
            self::Inactive  => 'bg-secondary',
            self::Suspended => 'bg-danger',
        };
    }

    /**
     * أيقونة الحالة (Tabler Icons)
     */
    public function icon(): string
    {
        return match($this) {
            self::Active    => 'ti ti-circle-check',
            self::Inactive  => 'ti ti-circle-minus',
            self::Suspended => 'ti ti-ban',
        };
    }

    /**
     * هل المستخدم يمكنه تسجيل الدخول؟
     */
    public function canLogin(): bool
    {
        return $this === self::Active;
    }

    /**
     * Array مناسب لحقل select
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}

