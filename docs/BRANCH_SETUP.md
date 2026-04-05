# نظام إدارة الفروع المستقلة - دليل التنفيذ الكامل

## 📋 نظرة عامة

تم تطبيق نظام متكامل يضمن أن كل فرع (Branch) يعمل بشكل مستقل تماماً مع عزل كامل للبيانات:

- 🎓 **الطلاب**: كل فرع له طلابه الخاصين
- 👨‍🏫 **المعلمين**: كل فرع له معلموه الخاصين
- 📚 **الحلقات**: كل فرع له حلقاته الخاصة
- 💰 **الحسابات**: كل فرع له مدفوعاته ونفقاته الخاصة
- 📊 **التقارير**: تقارير منفصلة لكل فرع

---

## 🔧 الملفات المضافة والمعدلة

### 1. **Traits** (السلوكيات)
```
app/Traits/
├── BranchScoped.php          ✅ فلترة تلقائية حسب الفرع
└── PreservesBranchId.php     ✅ الحفاظ على branch_id عند الحفظ
```

### 2. **Middleware** (وسطاء الطلبات)
```
app/Http/Middleware/
└── EnsureBranchAccess.php    ✅ التحقق من الوصول الآمن
```

### 3. **Policies** (سياسات الأذونات)
```
app/Policies/
└── BranchPolicy.php          ✅ التحقق من صلاحيات الوصول
```

### 4. **Services** (الخدمات)
```
app/Services/
└── BranchReportService.php   ✅ توليد التقارير منفصلة
```

### 5. **Controllers** (متحكمات المسارات)
```
app/Http/Controllers/Admin/
├── BranchReportController.php ✅ واجهة التقارير
└── StudentEnrollmentController.php (معدل) ✅ دعم تسجيل الطلاب الدفعي
```

### 6. **Migrations** (تعديلات قاعدة البيانات)
```
database/migrations/
├── 2026_04_05_000001_add_branch_id_to_missing_tables.php
├── 2026_04_05_000002_add_branch_id_to_enrollments_and_subscriptions.php
└── 2026_04_05_000003_populate_missing_branch_ids.php
```

### 7. **Models** (النماذج المعدلة)
```
app/Models/
├── Student.php                    ✅ إضافة BranchScoped
├── User.php                       ✅ إضافة BranchScoped
├── Group.php                      ✅ إضافة BranchScoped
├── Payment.php                    ✅ إضافة BranchScoped
├── Expense.php                    ✅ إضافة BranchScoped
├── StudentEnrollment.php          ✅ إضافة BranchScoped + branch_id
├── StudentSubscription.php        ✅ إضافة BranchScoped + branch_id
├── Assessment.php                 ✅ إضافة BranchScoped + branch_id
├── StudentProgressLog.php         ✅ إضافة BranchScoped + branch_id
├── TeacherAttendance.php          ✅ إضافة BranchScoped + branch_id
└── TeacherPayroll.php             ✅ إضافة BranchScoped + branch_id
```

### 8. **Documentation** (التوثيق)
```
docs/
├── branch-independence.md              ✅ دليل الاستخدام
├── branch-implementation-summary.md    ✅ ملخص التنفيذ
└── BRANCH_SETUP.md                     ✅ هذا الملف
```

---

## 🚀 خطوات التشغيل

### الخطوة 1️⃣: تشغيل Migrations
```bash
php artisan migrate
```

**ماذا سيحدث:**
- إضافة `branch_id` إلى 6 جداول
- ملء البيانات الموجودة بـ `branch_id` افتراضي
- تسجيل الفهارس للأداء الأفضل

### الخطوة 2️⃣: تسجيل Middleware (اختياري)
بالفعل مسجل في `bootstrap/app.php`:
```php
'branch.access' => \App\Http\Middleware\EnsureBranchAccess::class,
```

### الخطوة 3️⃣: إضافة Routes
في `routes/admin.php`، أضف:

```php
Route::prefix('reports')
    ->name('reports.')
    ->middleware(['auth', 'branch.access'])
    ->group(function () {
        Route::get('/', [BranchReportController::class, 'index'])->name('index');
        Route::get('/students', [BranchReportController::class, 'studentsReport'])->name('students');
        Route::get('/teachers', [BranchReportController::class, 'teachersReport'])->name('teachers');
        Route::get('/groups', [BranchReportController::class, 'groupsReport'])->name('groups');
        Route::get('/financial', [BranchReportController::class, 'financialReport'])->name('financial');
        Route::get('/attendance', [BranchReportController::class, 'attendanceReport'])->name('attendance');
        Route::get('/export', [BranchReportController::class, 'exportJson'])->name('export');
    });
```

### الخطوة 4️⃣: استخدام النظام

#### أ) في Controllers - حفظ البيانات بآمان:
```php
use App\Traits\PreservesBranchId;
use App\Http\Controllers\Admin\AdminController;

class YourController extends AdminController
{
    use PreservesBranchId;

    public function store(Request $request)
    {
        // إضافة branch_id تلقائياً من المستخدم الحالي
        $data = $this->ensureBranchId($request->validated());
        
        // التحقق من ملكية البيانات
        if (!$this->validateBranchOwnership($data)) {
            abort(403, 'غير مصرح لك بهذا الإجراء');
        }
        
        YourModel::create($data);
    }
}
```

#### ب) في Controllers - استعلام البيانات:
```php
public function index()
{
    // تُفلتر تلقائياً حسب فرع المستخدم الحالي
    $students = Student::all();
    
    return view('students.index', compact('students'));
}
```

#### ج) في Views - عرض معلومات الفرع:
```blade
@if($isSuperAdmin)
    <!-- عرض selector للفروع للمشرفين العامين -->
    <select id="branch-selector">
        @foreach($branches as $branch)
            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
        @endforeach
    </select>
@else
    <!-- عرض اسم الفرع الحالي -->
    <span class="badge">{{ auth()->user()->branch->name }}</span>
@endif
```

---

## 💡 أمثلة الاستخدام

### 1️⃣ الاستعلام عن البيانات:

**جلب طلاب الفرع الحالي (مفلتر تلقائياً):**
```php
$students = Student::all();
// يحتوي على طلاب فرع المستخدم الحالي فقط
```

**جلب طلاب فرع محدد:**
```php
$students = Student::forBranch(2)->get();
```

**عرض كل البيانات (للمشرفين العامين فقط):**
```php
if (auth()->user()->isSuperAdmin()) {
    $allStudents = Student::withoutBranchFilter()->get();
}
```

### 2️⃣ التحقق من الملكية:

```php
public function update(Student $student, Request $request)
{
    // إذا حاول مدير فرع A الوصول لطالب من فرع B، سيفشل تلقائياً
    // لأن BranchScoped Trait سيفلترها تلقائياً
    
    $student->update($request->validated());
}
```

### 3️⃣ التقارير:

```php
$service = new BranchReportService(auth()->user()->branch_id);

// الملخص الشامل
$summary = $service->getSummary();

// تقرير الطلاب
$students = $service->getStudentsReport();

// التقرير المالي
$financial = $service->getFinancialReport(
    $startDate = Carbon::parse('2026-01-01'),
    $endDate = Carbon::parse('2026-12-31')
);

// تقرير الحضور
$attendance = $service->getTeacherAttendanceReport();
```

### 4️⃣ إضافة بيانات جديدة:

```php
$data = [
    'full_name' => 'أحمد محمد',
    'age' => 15,
    'phone' => '01000000000',
];

// يضيف branch_id تلقائياً من فرع المستخدم
$data = $this->ensureBranchId($data);
$student = Student::create($data);
```

---

## 🔐 الأمان

### ✅ ما تم حمايته:

1. **الفلترة التلقائية**: جميع الاستعلامات تُفلتر تلقائياً
2. **منع الوصول المباشر**: لا يمكن الوصول لبيانات فرع آخر
3. **التحقق عند الحفظ**: يتم التحقق من ملكية البيانات
4. **Middleware الأمان**: فحص على كل طلب HTTP

### ⚠️ نقاط الانتباه:

- **المشرف العام** (`branch_id = null`) يرى كل البيانات
- **مدير الفرع** (`branch_id ≠ null`) يرى فرعه فقط
- **الحذف** مقيد على المشرفين العامين فقط
- **التعديل** متاح لمديري الفروع على بيانات فرعهم

---

## 📊 المخطط البياني

```
┌─────────────────┐
│  Super Admin    │  branch_id = null
│  (المشرف العام)  │  ✅ رؤية كل البيانات
└────────┬────────┘
         │
         ├─────────────────────────────────────────────┐
         │                                             │
    ┌────▼─────┐                               ┌──────▼──────┐
    │Branch A   │                               │ Branch B    │
    │(الفرع أ)   │                               │ (الفرع ب)    │
    └────┬─────┘                               └──────┬──────┘
         │                                            │
         ├─ User (Manager A)                        ├─ User (Manager B)
         ├─ Students (10)                           ├─ Students (15)
         ├─ Teachers (5)                            ├─ Teachers (8)
         ├─ Groups (3)                              ├─ Groups (4)
         ├─ Payments                                ├─ Payments
         └─ Expenses                                └─ Expenses
```

---

## 🔍 استكشاف الأخطاء

### المشكلة: طالب من فرع آخر يظهر

**السبب:** استخدام `withoutBranchFilter()`

**الحل:** تأكد من عدم استخدام `withoutBranchFilter()` إلا للمشرفين:
```php
$query = Student::query();
if (!auth()->user()->isSuperAdmin()) {
    // لا تستخدم withoutBranchFilter()
}
$students = $query->get();
```

### المشكلة: لا أستطيع إضافة بيانات جديدة

**السبب:** عدم إضافة `branch_id`

**الحل:** استخدم Trait:
```php
use PreservesBranchId;

$data = $this->ensureBranchId($data);
Student::create($data);
```

### المشكلة: المشرف العام لا يرى كل البيانات

**السبب:** لم يتم التحقق من `isSuperAdmin()`

**الحل:** استخدم:
```php
if (auth()->user()->isSuperAdmin()) {
    $students = Student::withoutBranchFilter()->get();
} else {
    $students = Student::all(); // مفلتر تلقائياً
}
```

---

## 📚 المصادر

- `docs/branch-independence.md` - دليل الاستخدام التفصيلي
- `docs/branch-implementation-summary.md` - ملخص التنفيذ
- `app/Traits/BranchScoped.php` - شرح الفلترة
- `app/Services/BranchReportService.php` - شرح الخدمات

---

## ✨ الخصائص الإضافية (قادمة)

- [ ] Dashboard منفصل لكل فرع
- [ ] تقارير PDF/Excel منفصلة
- [ ] Selector فروع للمشرفين العامين
- [ ] مزامنة البيانات بين الفروع (اختياري)
- [ ] إدارة مستخدمين منفصلة لكل فرع

---

## 📞 الدعم

للأسئلة أو الاستفسارات:
1. اطّلع على التوثيق أعلاه
2. تحقق من ملف `docs/branch-independence.md`
3. راجع أمثلة الاستخدام في `app/Services/BranchReportService.php`

---

**تم الانتهاء من التنفيذ ✅**

