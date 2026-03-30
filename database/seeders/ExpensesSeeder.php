<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ExpensesSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::query()->get();

        if ($branches->isEmpty()) {
            $this->command->warn('لا توجد فروع لإنشاء المصروفات.');
            return;
        }

        $expenseTemplates = [
            ['title' => 'إيجار المقر', 'min' => 3500, 'max' => 7000],
            ['title' => 'فاتورة الكهرباء', 'min' => 450, 'max' => 1200],
            ['title' => 'صيانة أجهزة ومكيفات', 'min' => 300, 'max' => 1800],
            ['title' => 'مستلزمات تعليمية', 'min' => 200, 'max' => 900],
            ['title' => 'ضيافة ونظافة', 'min' => 150, 'max' => 600],
        ];

        $count = 0;

        // مصروفات آخر 3 أشهر لكل فرع
        for ($m = 2; $m >= 0; $m--) {
            $baseDate = Carbon::now()->subMonths($m);

            foreach ($branches as $branch) {
                foreach ($expenseTemplates as $index => $template) {
                    // الإيجار مرة شهرية، والباقي 1-2 مرة شهرياً
                    $occurrences = $index === 0 ? 1 : random_int(1, 2);

                    for ($i = 0; $i < $occurrences; $i++) {
                        $day = min(random_int(1, 28), (int) $baseDate->copy()->endOfMonth()->day);
                        $expenseDate = $baseDate->copy()->day($day);
                        $amount = random_int($template['min'], $template['max']);

                        Expense::query()->create([
                            'branch_id' => $branch->id,
                            'expense_date' => $expenseDate->toDateString(),
                            'title' => $template['title'],
                            'amount' => $amount,
                            'notes' => null,
                        ]);

                        $count++;
                    }
                }
            }
        }

        $this->command->info("تم إنشاء {$count} سجل مصروفات بنجاح.");
    }
}

