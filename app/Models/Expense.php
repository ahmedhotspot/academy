<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $table = 'expenses';

    protected $fillable = [
        'branch_id',
        'expense_date',
        'title',
        'amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'expense_date' => 'date',
            'amount'       => 'decimal:2',
        ];
    }

    // =====================================================
    // العلاقات
    // =====================================================

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // =====================================================
    // Accessors
    // =====================================================

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' ج';
    }

    public function getFormattedDateAttribute(): string
    {
        return optional($this->expense_date)->format('Y-m-d');
    }
}

