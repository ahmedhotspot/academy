<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'student_id',
        'student_subscription_id',
        'payment_date',
        'amount',
        'receipt_number',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount'       => 'decimal:2',
        ];
    }

    // =====================================================
    // العلاقات
    // =====================================================

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(StudentSubscription::class, 'student_subscription_id');
    }

    // =====================================================
    // Accessors — Formatting
    // =====================================================

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' ر.س';
    }

    public function getReceiptFormattedAttribute(): string
    {
        return 'إيصال #' . $this->receipt_number;
    }

    public function getFormattedPaymentDateAttribute(): string
    {
        return optional($this->payment_date)->format('Y-m-d');
    }
}

