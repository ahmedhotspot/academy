# نظام استقلالية الفروع (Branch Independence System)

## نظرة عامة

نظام متكامل يضمن أن كل فرع (Branch) يعمل بشكل مستقل تماماً مع:
- **الطلاب**: كل فرع لديه طلابه الخاصين فقط
- **المعلمين**: كل فرع لديه معلموه الخاصين فقط
- **الحلقات**: كل فرع لديه حلقاته الخاصة فقط
- **الحسابات**: كل فرع لديه مدفوعاته ونفقاته الخاصة فقط
- **التقارير**: تقارير منفصلة لكل فرع

## كيفية التنفيذ

### 1. المستخدمون والفروع

#### المشرف العام (Super Admin)
```php
$user->branch_id = null; // بدون فرع = يرى كل البيانات
$user->isSuperAdmin(); // true
```

#### مدير الفرع (Branch Manager)
```php
$user->branch_id = 1; // الفرع رقم 1
$user->isSuperAdmin(); // false
```

### 2. الفلترة التلقائية (Global Scopes)

جميع Models المرتبطة بالفروع تستخدم `BranchScoped` Trait:

```php
use App\Traits\BranchScoped;

class Student extends Model
{
    use BranchScoped;
}
```

**الفلترة التلقائية:**
- إذا كان المستخدم مشرفاً عاماً: رؤية كل البيانات
- إذا كان مدير فرع: رؤية بيانات فرعه فقط

### 3. استخدام العمليات في Controllers

#### عند الاستعلام عن البيانات:
```php
// يتم الفلترة تلقائياً حسب الفرع
$students = Student::all();
$groups = Group::where('status', 'active')->get();
```

#### عند حفظ البيانات:
```php
use App\Traits\PreservesBranchId;

class StudentController extends AdminController
{
    use PreservesBranchId;

    public function store(StoreStudentRequest $request)
    {
        $data = $this->ensureBranchId($request->validated());
        $this->validateBranchOwnership($data);
        Student::create($data);
    }
}
```

#### إلغاء الفلترة (للمشرفين العامين فقط):
```php
$allStudents = Student::withoutBranchFilter()->get();
```

### 4. Models المدعومة

جميع Models التالية لديها `BranchScoped`:

- ✅ `Student` - الطلاب
- ✅ `User` - المستخدمين والمعلمين
- ✅ `Group` - الحلقات
- ✅ `Payment` - المدفوعات
- ✅ `Expense` - النفقات
- ✅ `StudentEnrollment` - تسجيل الطلاب
- ✅ `StudentSubscription` - اشتراكات الطلاب
- ✅ `Assessment` - التقييمات
- ✅ `StudentProgressLog` - سجلات التقدم
- ✅ `TeacherAttendance` - حضور المعلمين
- ✅ `TeacherPayroll` - مستحقات المعلمين

### 5. Middleware

استخدم `branch.access` Middleware في Routes:

```php
Route::middleware(['auth', 'branch.access'])->group(function () {
    Route::prefix('students')->group(function () {
        Route::get('/', [StudentController::class, 'index']);
        Route::post('/', [StudentController::class, 'store']);
    });
});
```

### 6. Policies والتحقق من الصلاحيات

```php
// في Controller
if (auth()->user()->can('view', $branchId)) {
    // عرض البيانات
}

// في Blade
@can('view', $branchId)
    <button>تعديل</button>
@endcan
```

### 7. استعلامات مخصصة

#### الحصول على بيانات فرع معين:
```php
$students = Student::forBranch(2)->get();
$groups = Group::forBranch(2)->get();
```

#### الحصول على بيانات الفرع الحالي:
```php
$currentBranchStudents = Student::currentBranch()->get();
```

## جداول قاعدة البيانات

تمت إضافة `branch_id` إلى الجداول التالية:

```sql
-- جداول موجودة (مع branch_id الجديد)
- students
- users
- groups
- payments
- expenses
- student_enrollments
- student_subscriptions

-- جداول جديدة (مع branch_id)
- teacher_attendances
- teacher_payrolls
- assessments
- student_progress_logs
```

## أمثلة عملية

### 1. عرض طلاب الفرع الحالي:
```php
public function index()
{
    $students = Student::all(); // مفلترة تلقائياً
    return view('students.index', compact('students'));
}
```

### 2. إضافة طالب جديد:
```php
public function store(StoreStudentRequest $request)
{
    $data = $request->validated();
    $data['branch_id'] = auth()->user()->branch_id; // أضف الفرع
    Student::create($data);
}
```

### 3. تقرير شامل لفرع محدد:
```php
public function getBranchReport($branchId)
{
    $this->authorize('viewReports', $branchId);
    
    return [
        'students' => Student::forBranch($branchId)->count(),
        'teachers' => User::forBranch($branchId)->where('role', 'teacher')->count(),
        'groups' => Group::forBranch($branchId)->count(),
        'revenue' => Payment::forBranch($branchId)->sum('amount'),
        'expenses' => Expense::forBranch($branchId)->sum('amount'),
    ];
}
```

### 4. التحقق من ملكية البيانات:
```php
public function edit($studentId)
{
    $student = Student::findOrFail($studentId);
    
    // التحقق تلقائي: الطالب يجب أن يكون من فرع المستخدم
    // إذا حاول مدير فرع الوصول لطالب من فرع آخر، سيفشل
    
    return view('students.edit', compact('student'));
}
```

## الأمان والملاحظات الهامة

1. **الفلترة التلقائية**: جميع الاستعلامات تُفلترت تلقائياً - لا تحتاج لكتابة فلاتر يدوية
2. **المشرفون العامون**: يمكنهم رؤية وتعديل كل شيء
3. **مديرو الفروع**: محصورون على فروعهم فقط
4. **الحفظ الآمن**: استخدم `PreservesBranchId` Trait عند الحفظ
5. **المتغيرات العامة**: بيانات مثل `StudyLevel` و `StudyTrack` تُشارك بين كل الفروع (بدون `branch_id`)

## الخطوات التالية (اختيارية)

- [ ] إضافة selector للفروع في Admin Panel للمشرفين
- [ ] إنشاء Dashboard منفصل لكل فرع
- [ ] تقارير Excel/PDF منفصلة لكل فرع
- [ ] إدارة مستخدمين منفصلة لكل فرع

