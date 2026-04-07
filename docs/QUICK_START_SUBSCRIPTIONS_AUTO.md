# نظام تنبيهات الاشتراكات - دليل البدء السريع (بدون Command)

## 🎯 ملخص الميزات

تم إضافة نظام **تلقائي بدون الحاجة لأوامر** لتنبيهات انتهاء الاشتراكات:

### 1. الاشتراكات القريبة الانتهاء (Approaching)
- 🕐 اشتراكات تنتهي خلال **يومين**
- تنبيهات **أصفر** في الواجهة
- شارات "قريب!" في الجداول

### 2. الاشتراكات المنتهية (Expired)
- ⏰ اشتراكات **انتهت بالفعل** (تاريخ الاستحقاق مضى)
- تنبيهات **أحمر** حادة
- شارات "منتهي" بارزة

### 3. إشعارات تلقائية (Auto-Notifications)
- ✅ **لا حاجة لـ Cron Job أو Command**
- تُنشأ تلقائياً عند **إنشاء أو تحديث** الاشتراك
- فوري وبدون تأخير

---

## ⚡ كيف يعمل النظام؟

### عند إنشاء اشتراك جديد:
```
1. ✅ يتم حفظ الاشتراك في قاعدة البيانات
2. 🔔 يتم فحص تاريخ الاستحقاق تلقائياً
3. 📢 إشعار يُرسل فوراً للمديرين (إن لزم الأمر)
4. 📊 الإحصائيات تُحدّث في الحال
```

### عند تحديث اشتراك:
```
1. ✅ يتم تحديث البيانات
2. 🔔 يتم فحص الحالة الجديدة
3. 📢 إشعار جديد إذا لزم الأمر
4. 📊 الإحصائيات تُحدّث مباشرة
```

---

## 📋 الملفات المضافة/المحدثة

### ملفات جديدة:
- `app/Services/Admin/SubscriptionNotificationService.php`

### ملفات محدثة:
- `app/Actions/Admin/StudentSubscriptions/CreateStudentSubscriptionAction.php` ✅
- `app/Actions/Admin/StudentSubscriptions/UpdateStudentSubscriptionAction.php` ✅
- `app/Models/StudentSubscription.php` ✅
- `app/Models/Notification.php` ✅
- `app/Services/Admin/StudentSubscriptionManagementService.php` ✅
- `resources/views/admin/student-subscriptions/index.blade.php` ✅
- `resources/views/admin/student-subscriptions/show.blade.php` ✅

---

## 🚀 الاستخدام الفوري

### لا تحتاج لفعل أي شيء!
النظام يعمل **تلقائياً** عند:
1. ✅ إضافة اشتراك جديد
2. ✅ تحديث اشتراك موجود
3. ✅ تغيير تاريخ الاستحقاق

### مثال:
```php
// في Controller
$subscription = app(CreateStudentSubscriptionAction::class)->handle([
    'student_id'    => 5,
    'fee_plan_id'   => 1,
    'amount'        => 500,
    'due_date'      => now()->addDays(2),  // قريب من الانتهاء
    // ... بيانات أخرى
]);

// ✅ الإشعار يُرسل تلقائياً للمديرين!
```

---

## 📊 الواجهة

### Dashboard
```
┌─────────────────────────────────────────┐
│ إجمالي: 100 | نشط: 70 | متأخر: 15      │
├─────────────────────────────────────────┤
│ ⏰ قريبة الانتهاء: 8 اشتراك              │
│ 🔴 منتهية: 5 اشتراكات                 │
└─────────────────────────────────────────┘
```

### صفحة التفاصيل
```
عند فتح اشتراك قريب الانتهاء:
┌─────────────────────────────────┐
│ ⏰ اشتراك قريب الانتهاء!        │
│ سينتهي في: 2 يوم              │
└─────────────────────────────────┘

عند فتح اشتراك منتهي:
┌─────────────────────────────────┐
│ ⚠️ اشتراك منتهي!               │
│ انتهى منذ 3 أيام              │
└─────────────────────────────────┘
```

### جدول الاشتراكات
```
تاريخ الاستحقاق: 2026-04-09 [قريب!] [منتهي]
```

---

## 🔔 الإشعارات

### نوع: financial (النوع الموحد)
```
عنوان: "متأخرات مالية: أحمد علي" أو "تنبيه سداد: أحمد علي"
الرسالة: "الطالب أحمد علي لديه مبلغ متبقي 500 ج وتاريخ الاستحقاق 2026-04-09"
البيانات:
- subscription_id: 1
- student_id: 5
- student_name: أحمد علي
- remaining: 500 ج
- due_date: 2026-04-09
- is_overdue: true/false
```

---

## 💻 استخدام البرمجي

```php
use App\Services\Admin\SubscriptionNotificationService;

// الحصول على الخدمة
$service = app(SubscriptionNotificationService::class);

// الاشتراكات القريبة الانتهاء
$approaching = $service->getApproachingExpirySubscriptions();
foreach ($approaching as $sub) {
    echo $sub->student->full_name . " - ينتهي في " . $sub->days_until_due . " يوم";
}

// الاشتراكات المنتهية
$expired = $service->getExpiredSubscriptions();

// الملخص
$summary = $service->getSummary();
echo "قريبة: " . $summary['approaching_count'];
echo "منتهية: " . $summary['expired_count'];

// إرسال إشعار يدوي (اختياري)
$service->notifyApproachingExpiry($subscription);

// إرسال إشعارات الجميع
$service->notifyAll();
```

---

## ⚙️ الإعدادات والتخصيص

### تغيير المدة (من يومين إلى X يوم)
في `SubscriptionNotificationService.php`:
```php
$inTwoDays = $today->copy()->addDays(2)->endOfDay();  // غيّر 2 إلى أي رقم
```

أو في `NotificationAutoCheckService.php`:
```php
->whereDate('due_date', '<=', $twoDays)  // عدّل الشروط
```

### تغيير رسالة الإشعار
في `NotificationAutoCheckService.php`:
```php
'title' => $isOverdue ? "متأخرات مالية: {$studentName}" : "تنبيه سداد: {$studentName}",
'message' => $isOverdue
    ? "الطالب {$studentName} لديه مبلغ متبقي {$remaining} وكان تاريخ الاستحقاق {$dueDate}."
    : "الطالب {$studentName} لديه مبلغ متبقي {$remaining} يستحق السداد بتاريخ {$dueDate}.",
```

---

## 📝 ملاحظات مهمة

1. ✅ **الإشعارات موجهة للمديرين** - يجب أن يكون المستخدم لديه صلاحيات
2. ✅ **عدم التكرار** - يتم فحص وتجنب الإشعارات المكررة تلقائياً
3. ✅ **التحديث الفوري** - الإحصائيات تُحدّث عند كل عملية
4. ✅ **لا تأثير على الأداء** - عمليات محسّنة وسريعة
5. ✅ **لا توجد جدولة معقدة** - كل شيء يعمل بدون Cron

---

## 🐛 استكشاف الأخطاء

### الإشعارات لا تظهر؟
```
تأكد من:
1. أن المستخدم لديه صلاحية (يجب أن يكون في نفس الفرع)
2. أن تاريخ الاستحقاق محدد في الاشتراك
3. أن الاشتراك لديه متبقي مالي
```

### الإحصائيات غير صحيحة؟
```
1. عد تحميل الصفحة (F5)
2. تأكد من أن البيانات صحيحة في قاعدة البيانات
```

### إشعارات مكررة؟
```
عادي جداً - النظام يفحص ويتجنب التكرار تلقائياً
الإشعارات القديمة تُحذف بعد ساعة
```

---

## 🎯 سيناريوهات الاستخدام

### السيناريو 1: إضافة اشتراك قريب الانتهاء
```
1. مدير يضيف اشتراك بتاريخ استحقاق خلال يومين
2. النظام يفحص التاريخ تلقائياً
3. إشعار أصفر يظهر للمديرين فوراً
4. صفحة الاشتراك تعرض تنبيه
```

### السيناريو 2: تجديد اشتراك منتهي
```
1. مدير يرى اشتراك منتهي (تنبيه أحمر)
2. يقوم بتحديث تاريخ الاستحقاق
3. النظام يفحص الحالة الجديدة
4. التنبيه يختفي والإشعار يُسحب
```

### السيناريو 3: دفع جزئي
```
1. طالب يدفع جزء من المتبقي
2. مدير يحدث الاشتراك
3. النظام يعيد حساب الإشعارات
4. الحالة تُحدّث تلقائياً
```

---

## 📚 معلومات إضافية

**اقرأ أيضاً:**
- `docs/SUBSCRIPTION_NOTIFICATIONS.md` - التفاصيل الكاملة
- `app/Services/Admin/NotificationAutoCheckService.php` - الكود الأساسي
- `app/Actions/Admin/StudentSubscriptions/` - معالجات الإنشاء والتحديث

---

## ✅ التحقق من الصحة

للتأكد من أن النظام يعمل:

```bash
# تفقد جدول الإشعارات
sqlite3 database/database.sqlite "SELECT * FROM notifications ORDER BY created_at DESC LIMIT 5;"

# تحقق من آخر اشتراكات
sqlite3 database/database.sqlite "SELECT id, student_id, due_date, remaining_amount, status FROM student_subscriptions ORDER BY updated_at DESC LIMIT 5;"
```

---

**✅ النظام جاهز والإشعارات تعمل تلقائياً الآن!**

لا تحتاج لفعل أي شيء إضافي - كل شيء يحدث بشكل تلقائي عند إنشاء أو تحديث الاشتراك.

