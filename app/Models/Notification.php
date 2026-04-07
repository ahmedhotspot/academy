<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'branch_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'data'    => 'json',
            'is_read' => 'boolean',
        ];
    }

    // =====================================================
    // الثوابت
    // =====================================================

    public const TYPES = ['absence', 'delay', 'report', 'financial'];

    // =====================================================
    // العلاقات
    // =====================================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // =====================================================
    // Accessors
    // =====================================================

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'absence'   => 'ti ti-clock-off',
            'delay'     => 'ti ti-alert-triangle',
            'report'    => 'ti ti-file-text',
            'financial' => 'ti ti-coin',
            default     => 'ti ti-bell',
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'absence'   => 'danger',
            'delay'     => 'warning',
            'report'    => 'info',
            'financial' => 'danger',
            default     => 'secondary',
        };
    }
}

