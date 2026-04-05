<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Student;
use App\Models\User;
use App\Models\Group;
use App\Models\Payment;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class BranchReportService
 *
 * خدمة توليد التقارير منفصلة لكل فرع
 */
class BranchReportService
{
    protected ?int $branchId;
    protected Branch $branch;

    public function __construct(?int $branchId = null)
    {
        if ($branchId) {
            $this->branchId = $branchId;
            $this->branch = Branch::findOrFail($branchId);
        } else {
            $this->branchId = auth()->user()?->branch_id;
            $this->branch = Branch::find($this->branchId);
        }
    }

    /**
     * الحصول على ملخص شامل للفرع
     */
    public function getSummary(): array
    {
        return [
            'branch_name' => $this->branch?->name,
            'students_count' => $this->getStudentsCount(),
            'teachers_count' => $this->getTeachersCount(),
            'groups_count' => $this->getGroupsCount(),
            'total_revenue' => $this->getTotalRevenue(),
            'total_expenses' => $this->getTotalExpenses(),
            'net_income' => $this->getNetIncome(),
        ];
    }

    /**
     * عدد الطلاب في الفرع
     */
    public function getStudentsCount(): int
    {
        return Student::forBranch($this->branchId)->count();
    }

    /**
     * عدد المعلمين في الفرع
     */
    public function getTeachersCount(): int
    {
        return User::forBranch($this->branchId)
            ->whereHas('roles', fn (Builder $q) => $q->where('name', 'المعلم'))
            ->count();
    }

    /**
     * عدد الحلقات في الفرع
     */
    public function getGroupsCount(): int
    {
        return Group::forBranch($this->branchId)->count();
    }

    /**
     * إجمالي الإيرادات في الفرع
     */
    public function getTotalRevenue(): float
    {
        return Payment::forBranch($this->branchId)->sum('amount') ?? 0;
    }

    /**
     * إجمالي النفقات في الفرع
     */
    public function getTotalExpenses(): float
    {
        return Expense::forBranch($this->branchId)->sum('amount') ?? 0;
    }

    /**
     * صافي الدخل (الإيرادات - النفقات)
     */
    public function getNetIncome(): float
    {
        return $this->getTotalRevenue() - $this->getTotalExpenses();
    }

    /**
     * تقرير الطلاب التفصيلي
     */
    public function getStudentsReport(): array
    {
        return Student::forBranch($this->branchId)
            ->with(['enrollments', 'subscriptions', 'payments'])
            ->get()
            ->map(fn (Student $student) => [
                'id' => $student->id,
                'name' => $student->full_name,
                'status' => $student->status,
                'current_group' => $student->currentEnrollment()?->group?->name,
                'subscriptions_count' => $student->subscriptions()->count(),
                'paid_amount' => $student->payments()->sum('amount'),
                'phone' => $student->phone,
            ])
            ->toArray();
    }

    /**
     * تقرير المعلمين التفصيلي
     */
    public function getTeachersReport(): array
    {
        return User::forBranch($this->branchId)
            ->whereHas('roles', fn (Builder $q) => $q->where('name', 'المعلم'))
            ->with(['teachingGroups', 'payrolls'])
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'status' => $user->status,
                'groups_count' => $user->teachingGroups()->count(),
                'pending_salary' => $user->payrolls()
                    ->where('status', '!=', 'مصروف')
                    ->sum('final_amount'),
            ])
            ->toArray();
    }

    /**
     * تقرير الحلقات التفصيلي
     */
    public function getGroupsReport(): array
    {
        return Group::forBranch($this->branchId)
            ->with(['teacher', 'studentEnrollments'])
            ->get()
            ->map(fn (Group $group) => [
                'id' => $group->id,
                'name' => $group->name,
                'teacher_name' => $group->teacher?->name,
                'students_count' => $group->studentEnrollments()
                    ->where('status', 'active')
                    ->count(),
                'status' => $group->status,
                'type' => $group->type_label,
            ])
            ->toArray();
    }

    /**
     * تقرير المالية التفصيلي
     */
    public function getFinancialReport(?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        $payments = Payment::forBranch($this->branchId);
        $expenses = Expense::forBranch($this->branchId);

        if ($startDate) {
            $payments = $payments->where('payment_date', '>=', $startDate);
            $expenses = $expenses->where('expense_date', '>=', $startDate);
        }

        if ($endDate) {
            $payments = $payments->where('payment_date', '<=', $endDate);
            $expenses = $expenses->where('expense_date', '<=', $endDate);
        }

        $paymentsByMonth = $payments->get()
            ->groupBy(fn ($p) => $p->payment_date->format('Y-m'))
            ->map(fn ($group) => $group->sum('amount'));

        $expensesByMonth = $expenses->get()
            ->groupBy(fn ($e) => $e->expense_date->format('Y-m'))
            ->map(fn ($group) => $group->sum('amount'));

        return [
            'total_payments' => $payments->sum('amount'),
            'total_expenses' => $expenses->sum('amount'),
            'payments_by_month' => $paymentsByMonth,
            'expenses_by_month' => $expensesByMonth,
        ];
    }

    /**
     * تقرير الحضور والغياب للمعلمين
     */
    public function getTeacherAttendanceReport(): array
    {
        return User::forBranch($this->branchId)
            ->whereHas('roles', fn (Builder $q) => $q->where('name', 'المعلم'))
            ->with('teacherAttendances')
            ->get()
            ->map(fn (User $user) => [
                'teacher_name' => $user->name,
                'total_days' => $user->teacherAttendances()->count(),
                'present' => $user->teacherAttendances()->where('status', 'حاضر')->count(),
                'absent' => $user->teacherAttendances()->where('status', 'غائب')->count(),
                'late' => $user->teacherAttendances()->where('status', 'متأخر')->count(),
                'excused' => $user->teacherAttendances()->where('status', 'بعذر')->count(),
            ])
            ->toArray();
    }
}

