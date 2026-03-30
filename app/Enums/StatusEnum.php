<?php

namespace App\Enums;

enum StatusEnum: string
{
    case Active   = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';

    /**
     * التسمية العربية للحالة
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
     * CSS class الخاص بالـ badge
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
     * جميع الحالات كـ array مناسب للـ select
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn($case) => [$case->value => $case->label()]
        )->toArray();
    }
}

