# Subscription Expiry Notification System

## نظام تنبيهات انتهاء الاشتراكات

### Overview

هذا النظام يوفر:
1. **تنبيهات ذكية** عند اقتراب موعد انتهاء الاشتراك (خلال يومين)
2. **تنبيهات حول الاشتراكات المنتهية** (تاريخ الاستحقاق مضى)
3. **واجهة مستخدم محسّنة** تعرض حالة الاشتراكات بشكل واضح
4. **إشعارات موجهة للمديرين** (وليست عادية/عامة)

---

## Features المضافة

### 1. نماذج جديدة (Scopes)

تمت إضافة نطاقات جديدة في `StudentSubscription`:

```php
// الاشتراكات القريبة الانتهاء (خلال يومين)
StudentSubscription::approachingExpiry()->get();

// الاشتراكات المنتهية (تاريخ الاستحقاق مضى)
StudentSubscription::hasExpiredDueDate()->get();
```

### 2. خدمة الإشعارات الجديدة

تم إنشاء `SubscriptionNotificationService` في:
```
app/Services/Admin/SubscriptionNotificationService.php
```

**الوظائف الرئيسية:**
- `getApproachingExpirySubscriptions()` - الاشتراكات القريبة
- `getExpiredSubscriptions()` - الاشتراكات المنتهية
- `notifyApproachingExpiry()` - إرسال تنبيه قريب الانتهاء
- `notifyExpired()` - إرسال تنبيه منتهي
- `getSummary()` - إحصائيات الاشتراكات

### 3. أنواع إشعارات جديدة

تمت إضافة نوعان جديدان من الإشعارات:
- `subscription_approaching` - ⏰ اشتراك قريب الانتهاء
- `subscription_expired` - 🔔 اشتراك منتهي

### 4. Artisan Command

```bash
# تشغيل يدوي
php artisan subscriptions:notify-expiry --all

# فقط الاشتراكات القريبة
php artisan subscriptions:notify-expiry --approaching

# فقط الاشتراكات المنتهية
php artisan subscriptions:notify-expiry --expired
```

### 5. جدولة تلقائية

تم إضافة جدولة في `routes/console.php`:
- **09:00** - إرسال إشعارات الاشتراكات
- **15:00** - إرسال إشعارات الاشتراكات (مرة أخرى)

### 6. واجهة المستخدم المحسّنة

#### Dashboard Cards
- عرض عدد الاشتراكات **القريبة الانتهاء**
- عرض عدد الاشتراكات **المنتهية**
- بطاقات ملونة بألوان تحذيرية

#### Alerts على صفحة التفاصيل
- تنبيه أحمر للاشتراكات المنتهية
- تنبيه أصفر للاشتراكات القريبة

#### جدول البيانات
- عرض شارة "قريب!" للاشتراكات القريبة
- عرض شارة "منتهي" للاشتراكات المنتهية

---

## الإشعارات (Notifications)

### نوع: subscription_approaching

```php
[
    'type'       => 'subscription_approaching',
    'title'      => '⏰ اشتراك قريب الانتهاء',
    'message'    => 'الطالب أحمد علي - الاشتراك (الخطة الذهبية) سينتهي خلال يومين',
    'data'       => [
        'subscription_id' => 1,
        'student_id'      => 5,
        'days_remaining'  => 2,
        'due_date'        => '2026-04-09',
    ]
]
```

### نوع: subscription_expired

```php
[
    'type'       => 'subscription_expired',
    'title'      => '🔔 اشتراك منتهي',
    'message'    => 'الطالب محمد سالم - الاشتراك (الخطة الفضية) انتهى منذ 3 أيام',
    'data'       => [
        'subscription_id' => 2,
        'student_id'      => 10,
        'days_overdue'    => 3,
        'expired_date'    => '2026-04-04',
    ]
]
```

---

## الملخص الإحصائي (Report Summary)

تمت إضافة حقول جديدة:

```php
$reportSummary = [
    'total'                 => 100,
    'active'                => 70,
    'overdue'               => 15,
    'complete'              => 10,
    'suspended'             => 5,
    'overdueStudents'       => 12,  // عدد الطلاب المختلفين
    'approachingExpiry'     => 8,   // اشتراكات قريبة الانتهاء
    'expiredSubscriptions'  => 5,   // اشتراكات منتهية
];
```

---

## سير العمل (Workflow)

### عند اقتراب الاشتراك من الانتهاء:

1. يتم تشغيل الأمر (`subscriptions:notify-expiry --all`)
2. البحث عن الاشتراكات خلال يومين من الانتهاء
3. حذف الإشعارات القديمة (تجنب التكرار)
4. إنشاء إشعار جديد لكل مدير على الفرع
5. يظهر الإشعار في Dashboard

### الواجهة:

- قائمة الاشتراكات تظهر شارة "قريب!" بجانب تاريخ الاستحقاق
- صفحة التفاصيل تعرض تنبيه أصفر بولون واضح
- الملخص يعرض عدد الاشتراكات القريبة

### عند انتهاء الاشتراك:

1. يتم تشغيل الأمر (`subscriptions:notify-expiry --all`)
2. البحث عن الاشتراكات المنتهية (تاريخ الاستحقاق في الماضي)
3. إنشاء إشعار لكل مدير
4. الواجهة تعرض شارة "منتهي" وتنبيه أحمر

---

## الاستخدام (Usage)

### تشغيل الأوامر يدويًا:

```bash
# تشغيل كامل
php artisan subscriptions:notify-expiry --all

# فقط الاشتراكات القريبة
php artisan subscriptions:notify-expiry --approaching

# فقط المنتهية
php artisan subscriptions:notify-expiry --expired
```

### في الكود:

```php
use App\Services\Admin\SubscriptionNotificationService;

$service = app(SubscriptionNotificationService::class);

// الحصول على الاشتراكات
$approaching = $service->getApproachingExpirySubscriptions();
$expired = $service->getExpiredSubscriptions();

// الملخص
$summary = $service->getSummary();
// ['approaching_count' => 8, 'expired_count' => 5]

// إرسال إشعار
$service->notifyApproachingExpiry($subscription);
```

---

## الملفات المُضافة/المُعدّلة

### ملفات جديدة:
- `app/Services/Admin/SubscriptionNotificationService.php`
- `app/Console/Commands/SendSubscriptionExpiryNotifications.php`

### ملفات معدّلة:
- `app/Models/StudentSubscription.php` - إضافة Scopes
- `app/Models/Notification.php` - أنواع إشعارات جديدة
- `app/Services/Admin/StudentSubscriptionManagementService.php` - إحصائيات محدّثة
- `resources/views/admin/student-subscriptions/index.blade.php` - بطاقات تنبيهات جديدة
- `resources/views/admin/student-subscriptions/show.blade.php` - تنبيهات بصرية
- `routes/console.php` - جدولة جديدة

---

## ملاحظات مهمة

1. **الإشعارات موجهة للمديرين فقط** - يتم حفظها في جدول `notifications`
2. **التحديث التلقائي** - تُحدّث الإحصائيات كل مرة يتم فيها زيارة الصفحة
3. **لا إشعارات مكررة** - يتم حذف الإشعارات القديمة قبل إضافة جديدة
4. **تقارير واضحة** - الألوان والأيقونات توضح الحالة بسرعة

---

## مثال عملي

```php
// في Controller أو Service
use App\Services\Admin\SubscriptionNotificationService;

class DashboardController {
    public function index(SubscriptionNotificationService $service) {
        $summary = $service->getSummary();
        
        return view('dashboard', [
            'approachingCount'  => $summary['approaching_count'],
            'expiredCount'      => $summary['expired_count'],
        ]);
    }
}
```

```blade
<!-- في View -->
@if($approachingCount > 0)
    <div class="alert alert-warning">
        {{ $approachingCount }} اشتراك قريب الانتهاء
    </div>
@endif

@if($expiredCount > 0)
    <div class="alert alert-danger">
        {{ $expiredCount }} اشتراك منتهي
    </div>
@endif
```

---

## Troubleshooting

### الإشعارات لا تظهر؟

1. تأكد من تشغيل الـ Scheduler:
   ```bash
   php artisan schedule:work
   ```

2. أو شغّل الأمر يدويًا:
   ```bash
   php artisan subscriptions:notify-expiry --all
   ```

3. تحقق من أن المستخدمين لديهم دور "admin"

### الإحصائيات غير محدّثة؟

قم بتحديث الصفحة أو استدعاء:
```php
$summary = app(SubscriptionNotificationService::class)->getSummary();
```

---

**تم إنشاء هذا النظام لضمان عدم نسيان انتهاء الاشتراكات وتتبعها بدقة!** ✓

