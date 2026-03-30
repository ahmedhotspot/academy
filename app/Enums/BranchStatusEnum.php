<?php

namespace App\Enums;

enum BranchStatusEnum: string
{
    case Active   = 'active';
    case Inactive = 'inactive';

    /**
     * التسمية العربية
     */
    public function label(): string
    {
        return match($this) {
            self::Active   => 'نشط',
            self::Inactive => 'غير نشط',
        };
    }

    /**
     * CSS class للـ badge
     */
    public function badgeClass(): string
    {
        return match($this) {
            self::Active   => 'bg-success',
            self::Inactive => 'bg-secondary',
        };
    }

    /**
     * أيقونة الحالة (Tabler Icons)
     */
    public function icon(): string
    {
        return match($this) {
            self::Active   => 'ti ti-circle-check',
            self::Inactive => 'ti ti-circle-minus',
        };
    }

    /**
     * Array مناسب لحقل select في الـ views
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}

