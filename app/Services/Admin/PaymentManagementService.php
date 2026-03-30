<?php

namespace App\Services\Admin;

use App\Models\Payment;
use App\Models\Student;
use App\Services\BaseService;
use Illuminate\Http\Request;

class PaymentManagementService extends BaseService
{
    /**
     * بيانات DataTable Ajax
     */
    public function datatable(Request $request): array
    {
        $draw   = (int) $request->input('draw', 1);
        $start  = max((int) $request->input('start', 0), 0);
        $length = max((int) $request->input('length', 10), 1);
        $search = trim((string) data_get($request->input('search'), 'value', ''));

        $baseQuery = Payment::query()
            ->with(['student', 'subscription']);

        if ($request->filled('student_id')) {
            $baseQuery->where('student_id', $request->input('student_id'));
        }

        $recordsTotal = Payment::query()->count();

        if ($search !== '') {
            $baseQuery->where(function ($q) use ($search) {
                $q->whereHas('student', fn ($s) => $s->where('full_name', 'like', "%{$search}%"))
                    ->orWhere('receipt_number', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $rows = $baseQuery
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->skip($start)
            ->take($length)
            ->get();

        $data = $rows->map(function (Payment $payment) {
            return [
                'id'                => $payment->id,
                'student_id'        => $payment->student_id,
                'student_name'      => $payment->student?->full_name ?? '-',
                'receipt_number'    => $payment->receipt_number,
                'receipt_formatted' => $payment->receipt_formatted,
                'payment_date'      => $payment->formatted_payment_date,
                'amount'            => $payment->amount,
                'formatted_amount'  => $payment->formatted_amount,
                'notes'             => $payment->notes ? substr($payment->notes, 0, 50) . '...' : '-',
            ];
        })->values()->all();

        return [
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ];
    }

    /**
     * سجل الدفعات للطالب
     */
    public function getStudentPayments(Student $student): array
    {
        $payments = $student->payments()
            ->with(['subscription'])
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->get()
            ->map(fn ($p) => [
                'id'              => $p->id,
                'receipt'         => $p->receipt_formatted,
                'date'            => $p->formatted_payment_date,
                'amount'          => $p->formatted_amount,
                'subscription'    => $p->subscription?->student_id ? 'اشتراك نشط' : '-',
            ])
            ->toArray();

        $lastPayment = $student->payments()
            ->with(['subscription'])
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->first();

        $totalPaid = $student->payments()->sum('amount');

        return [
            'payments'     => $payments,
            'lastPayment'  => $lastPayment,
            'totalPaid'    => $totalPaid,
            'count'        => count($payments),
        ];
    }

    /**
     * ملخص إحصائي
     */
    public function reportSummary(array $filters = []): array
    {
        $query = Payment::query();

        if (! empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        $totalPayments = (clone $query)->count();
        $totalAmount = (clone $query)->sum('amount');
        $averagePayment = $totalPayments > 0 ? round($totalAmount / $totalPayments, 2) : 0;

        return [
            'total'           => $totalPayments,
            'totalAmount'     => $totalAmount,
            'averagePayment'  => $averagePayment,
        ];
    }
}

