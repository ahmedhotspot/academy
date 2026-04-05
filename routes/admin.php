<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\GroupScheduleController;
use App\Http\Controllers\Admin\GuardianController;
use App\Http\Controllers\Admin\PagePatternController;
use App\Http\Controllers\Admin\StudyLevelController;
use App\Http\Controllers\Admin\StudyTrackController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherAttendanceController;
use App\Http\Controllers\Admin\StudentEnrollmentController;
use App\Http\Controllers\Admin\StudentProgressLogController;
use App\Http\Controllers\Admin\AssessmentController;
use App\Http\Controllers\Admin\FeePlanController;
use App\Http\Controllers\Admin\StudentSubscriptionController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\TeacherPayrollController;
use App\Http\Controllers\Admin\TeacherManagementController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ImportExportController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| جميع مسارات لوحة التحكم تحت prefix: /admin
| جميع أسماء الـ routes تحت: admin.*
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'role:المشرف العام|السكرتيرة|المعلم'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        /*
        |------------------------------------------------------------------
        | قوالب الصفحات الأساسية (بدون CRUD تشغيلي)
        |------------------------------------------------------------------
        | الهدف: توحيد نمط index/create/edit/show + DataTable Ajax
        */
        Route::prefix('page-patterns')
            ->name('page-patterns.')
            ->group(function () {
                Route::get('/', [PagePatternController::class, 'index'])->name('index');
                Route::get('/datatable', [PagePatternController::class, 'datatable'])->name('datatable');
                Route::get('/create', [PagePatternController::class, 'create'])->name('create');
                Route::get('/{id}/edit', [PagePatternController::class, 'edit'])
                    ->whereNumber('id')
                    ->name('edit');
                Route::get('/{id}', [PagePatternController::class, 'show'])
                    ->whereNumber('id')
                    ->name('show');
            });

        /*
        |------------------------------------------------------------------
        | إدارة الفروع
        |------------------------------------------------------------------
        */
        Route::prefix('branches')
            ->name('branches.')
            ->group(function () {
                Route::get('/', [BranchController::class, 'index'])
                    ->middleware('permission:branches.view')
                    ->name('index');

                Route::get('/datatable', [BranchController::class, 'datatable'])
                    ->middleware('permission:branches.view')
                    ->name('datatable');

                Route::get('/create', [BranchController::class, 'create'])
                    ->middleware('permission:branches.create')
                    ->name('create');

                Route::post('/', [BranchController::class, 'store'])
                    ->middleware('permission:branches.create')
                    ->name('store');

                Route::get('/{branch}', [BranchController::class, 'show'])
                    ->middleware('permission:branches.view')
                    ->name('show');

                Route::get('/{branch}/edit', [BranchController::class, 'edit'])
                    ->middleware('permission:branches.update')
                    ->name('edit');

                Route::put('/{branch}', [BranchController::class, 'update'])
                    ->middleware('permission:branches.update')
                    ->name('update');

                Route::delete('/{branch}', [BranchController::class, 'destroy'])
                    ->middleware('permission:branches.delete')
                    ->name('destroy');
            });

        /*
        |------------------------------------------------------------------
        | إدارة الطلاب
        |------------------------------------------------------------------
        */
        Route::prefix('students')
            ->name('students.')
            ->group(function () {
                Route::get('/', [StudentController::class, 'index'])
                    ->middleware('permission:students.view')
                    ->name('index');

                Route::get('/datatable', [StudentController::class, 'datatable'])
                    ->middleware('permission:students.view')
                    ->name('datatable');

                Route::get('/create', [StudentController::class, 'create'])
                    ->middleware('permission:students.create')
                    ->name('create');

                Route::post('/', [StudentController::class, 'store'])
                    ->middleware('permission:students.create')
                    ->name('store');

                Route::get('/{student}', [StudentController::class, 'show'])
                    ->middleware('permission:students.view')
                    ->name('show');

                Route::get('/{student}/edit', [StudentController::class, 'edit'])
                    ->middleware('permission:students.update')
                    ->name('edit');

                Route::put('/{student}', [StudentController::class, 'update'])
                    ->middleware('permission:students.update')
                    ->name('update');

                Route::delete('/{student}', [StudentController::class, 'destroy'])
                    ->middleware('permission:students.delete')
                    ->name('destroy');

                Route::post('/{student}/set-portal-password', [StudentController::class, 'setPortalPassword'])
                    ->middleware('permission:students.update')
                    ->name('set-portal-password');
            });

        /*
        |------------------------------------------------------------------
        | إدارة أولياء الأمور
        |------------------------------------------------------------------
        */
        Route::prefix('guardians')
            ->name('guardians.')
            ->group(function () {
                Route::get('/', [GuardianController::class, 'index'])
                    ->middleware('permission:guardians.view')
                    ->name('index');

                Route::get('/datatable', [GuardianController::class, 'datatable'])
                    ->middleware('permission:guardians.view')
                    ->name('datatable');

                Route::get('/create', [GuardianController::class, 'create'])
                    ->middleware('permission:guardians.create')
                    ->name('create');

                Route::post('/', [GuardianController::class, 'store'])
                    ->middleware('permission:guardians.create')
                    ->name('store');

                Route::get('/{guardian}', [GuardianController::class, 'show'])
                    ->middleware('permission:guardians.view')
                    ->name('show');

                Route::get('/{guardian}/edit', [GuardianController::class, 'edit'])
                    ->middleware('permission:guardians.update')
                    ->name('edit');

                Route::put('/{guardian}', [GuardianController::class, 'update'])
                    ->middleware('permission:guardians.update')
                    ->name('update');

                Route::delete('/{guardian}', [GuardianController::class, 'destroy'])
                    ->middleware('permission:guardians.delete')
                    ->name('destroy');

                Route::post('/{guardian}/set-portal-password', [GuardianController::class, 'setPortalPassword'])
                    ->middleware('permission:guardians.update')
                    ->name('set-portal-password');
            });

        /*
        |------------------------------------------------------------------
        | إدارة المستويات
        |------------------------------------------------------------------
        */
        Route::prefix('study-levels')
            ->name('study-levels.')
            ->group(function () {
                Route::get('/', [StudyLevelController::class, 'index'])
                    ->middleware('permission:study-levels.view')
                    ->name('index');

                Route::get('/datatable', [StudyLevelController::class, 'datatable'])
                    ->middleware('permission:study-levels.view')
                    ->name('datatable');

                Route::get('/create', [StudyLevelController::class, 'create'])
                    ->middleware('permission:study-levels.create')
                    ->name('create');

                Route::post('/', [StudyLevelController::class, 'store'])
                    ->middleware('permission:study-levels.create')
                    ->name('store');

                Route::get('/{studyLevel}', [StudyLevelController::class, 'show'])
                    ->middleware('permission:study-levels.view')
                    ->name('show');

                Route::get('/{studyLevel}/edit', [StudyLevelController::class, 'edit'])
                    ->middleware('permission:study-levels.update')
                    ->name('edit');

                Route::put('/{studyLevel}', [StudyLevelController::class, 'update'])
                    ->middleware('permission:study-levels.update')
                    ->name('update');

                Route::delete('/{studyLevel}', [StudyLevelController::class, 'destroy'])
                    ->middleware('permission:study-levels.delete')
                    ->name('destroy');
            });

        /*
        |------------------------------------------------------------------
        | إدارة المسارات
        |------------------------------------------------------------------
        */
        Route::prefix('study-tracks')
            ->name('study-tracks.')
            ->group(function () {
                Route::get('/', [StudyTrackController::class, 'index'])
                    ->middleware('permission:study-tracks.view')
                    ->name('index');

                Route::get('/datatable', [StudyTrackController::class, 'datatable'])
                    ->middleware('permission:study-tracks.view')
                    ->name('datatable');

                Route::get('/create', [StudyTrackController::class, 'create'])
                    ->middleware('permission:study-tracks.create')
                    ->name('create');

                Route::post('/', [StudyTrackController::class, 'store'])
                    ->middleware('permission:study-tracks.create')
                    ->name('store');

                Route::get('/{studyTrack}', [StudyTrackController::class, 'show'])
                    ->middleware('permission:study-tracks.view')
                    ->name('show');

                Route::get('/{studyTrack}/edit', [StudyTrackController::class, 'edit'])
                    ->middleware('permission:study-tracks.update')
                    ->name('edit');

                Route::put('/{studyTrack}', [StudyTrackController::class, 'update'])
                    ->middleware('permission:study-tracks.update')
                    ->name('update');

                Route::delete('/{studyTrack}', [StudyTrackController::class, 'destroy'])
                    ->middleware('permission:study-tracks.delete')
                    ->name('destroy');
            });

        /*
        |------------------------------------------------------------------
        | إدارة الحلقات
        |------------------------------------------------------------------
        */
        Route::prefix('groups')
            ->name('groups.')
            ->group(function () {
                Route::get('/', [GroupController::class, 'index'])
                    ->middleware('permission:groups.view')
                    ->name('index');

                Route::get('/datatable', [GroupController::class, 'datatable'])
                    ->middleware('permission:groups.view')
                    ->name('datatable');

                Route::get('/create', [GroupController::class, 'create'])
                    ->middleware('permission:groups.create')
                    ->name('create');

                Route::post('/', [GroupController::class, 'store'])
                    ->middleware('permission:groups.create')
                    ->name('store');

                Route::get('/{group}', [GroupController::class, 'show'])
                    ->middleware('permission:groups.view')
                    ->name('show');

                Route::get('/{group}/edit', [GroupController::class, 'edit'])
                    ->middleware('permission:groups.update')
                    ->name('edit');

                Route::put('/{group}', [GroupController::class, 'update'])
                    ->middleware('permission:groups.update')
                    ->name('update');

                Route::delete('/{group}', [GroupController::class, 'destroy'])
                    ->middleware('permission:groups.delete')
                    ->name('destroy');
            });

        /*
        |------------------------------------------------------------------
        | إدارة جداول الحلقات
        |------------------------------------------------------------------
        */
        Route::prefix('group-schedules')
            ->name('group-schedules.')
            ->group(function () {
                Route::get('/', [GroupScheduleController::class, 'index'])
                    ->middleware('permission:group-schedules.view')
                    ->name('index');

                Route::get('/datatable', [GroupScheduleController::class, 'datatable'])
                    ->middleware('permission:group-schedules.view')
                    ->name('datatable');

                Route::get('/create', [GroupScheduleController::class, 'create'])
                    ->middleware('permission:group-schedules.create')
                    ->name('create');

                Route::post('/', [GroupScheduleController::class, 'store'])
                    ->middleware('permission:group-schedules.create')
                    ->name('store');

                Route::get('/{groupSchedule}', [GroupScheduleController::class, 'show'])
                    ->middleware('permission:group-schedules.view')
                    ->name('show');

                Route::get('/{groupSchedule}/edit', [GroupScheduleController::class, 'edit'])
                    ->middleware('permission:group-schedules.update')
                    ->name('edit');

                Route::put('/{groupSchedule}', [GroupScheduleController::class, 'update'])
                    ->middleware('permission:group-schedules.update')
                    ->name('update');

                Route::delete('/{groupSchedule}', [GroupScheduleController::class, 'destroy'])
                    ->middleware('permission:group-schedules.delete')
                    ->name('destroy');
            });

        /*
        |------------------------------------------------------------------
        | إدارة تسجيل الطلاب داخل الحلقات
        |------------------------------------------------------------------
        */
        Route::prefix('student-enrollments')
            ->name('student-enrollments.')
            ->group(function () {
                Route::get('/', [StudentEnrollmentController::class, 'index'])
                    ->middleware('permission:student-enrollments.view')
                    ->name('index');

                Route::get('/datatable', [StudentEnrollmentController::class, 'datatable'])
                    ->middleware('permission:student-enrollments.view')
                    ->name('datatable');

                Route::get('/create', [StudentEnrollmentController::class, 'create'])
                    ->middleware('permission:student-enrollments.create')
                    ->name('create');

                Route::post('/', [StudentEnrollmentController::class, 'store'])
                    ->middleware('permission:student-enrollments.create')
                    ->name('store');

                Route::get('/student/{student}', [StudentEnrollmentController::class, 'show'])
                    ->middleware('permission:student-enrollments.view')
                    ->name('show');

                Route::get('/{studentEnrollment}/edit', [StudentEnrollmentController::class, 'edit'])
                    ->middleware('permission:student-enrollments.update')
                    ->name('edit');

                Route::put('/{studentEnrollment}', [StudentEnrollmentController::class, 'update'])
                    ->middleware('permission:student-enrollments.update')
                    ->name('update');

                Route::delete('/{studentEnrollment}', [StudentEnrollmentController::class, 'destroy'])
                    ->middleware('permission:student-enrollments.delete')
                    ->name('destroy');
            });

        /*
        |------------------------------------------------------------------
        | إدارة حضور وغياب المعلمين
        |------------------------------------------------------------------
        */
        Route::prefix('teacher-attendances')
            ->name('teacher-attendances.')
            ->group(function () {
                Route::get('/', [TeacherAttendanceController::class, 'index'])
                    ->middleware('permission:teacher-attendances.view')
                    ->name('index');

                Route::get('/datatable', [TeacherAttendanceController::class, 'datatable'])
                    ->middleware('permission:teacher-attendances.view')
                    ->name('datatable');

                Route::get('/create', [TeacherAttendanceController::class, 'create'])
                    ->middleware('permission:teacher-attendances.create')
                    ->name('create');

                Route::post('/', [TeacherAttendanceController::class, 'store'])
                    ->middleware('permission:teacher-attendances.create')
                    ->name('store');

                Route::get('/teacher/{teacher}', [TeacherAttendanceController::class, 'show'])
                    ->middleware('permission:teacher-attendances.view')
                    ->name('show');

                Route::get('/{teacherAttendance}/edit', [TeacherAttendanceController::class, 'edit'])
                    ->middleware('permission:teacher-attendances.update')
                    ->name('edit');

                Route::put('/{teacherAttendance}', [TeacherAttendanceController::class, 'update'])
                    ->middleware('permission:teacher-attendances.update')
                    ->name('update');

                Route::delete('/{teacherAttendance}', [TeacherAttendanceController::class, 'destroy'])
                    ->middleware('permission:teacher-attendances.delete')
                    ->name('destroy');
            });

        /*
        |------------------------------------------------------------------
        | المتابعة التعليمية اليومية
        |------------------------------------------------------------------
        */
        Route::prefix('student-progress-logs')
            ->name('student-progress-logs.')
            ->group(function () {
                Route::get('/', [StudentProgressLogController::class, 'index'])
                    ->middleware('permission:student-progress-logs.view')
                    ->name('index');

                Route::get('/datatable', [StudentProgressLogController::class, 'datatable'])
                    ->middleware('permission:student-progress-logs.view')
                    ->name('datatable');

                Route::get('/students-by-group', [StudentProgressLogController::class, 'studentsByGroup'])
                    ->middleware('permission:student-progress-logs.create')
                    ->name('students-by-group');

                Route::get('/create', [StudentProgressLogController::class, 'create'])
                    ->middleware('permission:student-progress-logs.create')
                    ->name('create');

                Route::post('/', [StudentProgressLogController::class, 'store'])
                    ->middleware('permission:student-progress-logs.create')
                    ->name('store');

                Route::get('/student/{student}', [StudentProgressLogController::class, 'show'])
                    ->middleware('permission:student-progress-logs.view')
                    ->name('show');

                Route::get('/{studentProgressLog}/edit', [StudentProgressLogController::class, 'edit'])
                    ->middleware('permission:student-progress-logs.update')
                    ->name('edit');

                Route::put('/{studentProgressLog}', [StudentProgressLogController::class, 'update'])
                    ->middleware('permission:student-progress-logs.update')
                    ->name('update');

                Route::delete('/{studentProgressLog}', [StudentProgressLogController::class, 'destroy'])
                    ->middleware('permission:student-progress-logs.delete')
                    ->name('destroy');
            });

        /*
        |------------------------------------------------------------------
        | نظام الاختبارات
        |------------------------------------------------------------------
        */
        Route::prefix('assessments')
            ->name('assessments.')
            ->group(function () {
                Route::get('/', [AssessmentController::class, 'index'])
                    ->middleware('permission:assessments.view')
                    ->name('index');

                Route::get('/datatable', [AssessmentController::class, 'datatable'])
                    ->middleware('permission:assessments.view')
                    ->name('datatable');

                Route::get('/students-by-group', [AssessmentController::class, 'studentsByGroup'])
                    ->middleware('permission:assessments.create')
                    ->name('students-by-group');

                Route::get('/create', [AssessmentController::class, 'create'])
                    ->middleware('permission:assessments.create')
                    ->name('create');

                Route::post('/', [AssessmentController::class, 'store'])
                    ->middleware('permission:assessments.create')
                    ->name('store');

                Route::get('/student/{student}', [AssessmentController::class, 'show'])
                    ->middleware('permission:assessments.view')
                    ->name('show');

                Route::get('/{assessment}/edit', [AssessmentController::class, 'edit'])
                    ->middleware('permission:assessments.update')
                    ->name('edit');

                Route::put('/{assessment}', [AssessmentController::class, 'update'])
                    ->middleware('permission:assessments.update')
                    ->name('update');

                Route::delete('/{assessment}', [AssessmentController::class, 'destroy'])
                    ->middleware('permission:assessments.delete')
                    ->name('destroy');
            });

        /*
        |------------------------------------------------------------------
        | إدارة خطط الرسوم والاشتراكات
        |------------------------------------------------------------------
        */
        Route::prefix('fee-plans')
            ->name('fee-plans.')
            ->group(function () {
                Route::get('/', [FeePlanController::class, 'index'])
                    ->middleware('permission:fee-plans.view')
                    ->name('index');

                Route::get('/datatable', [FeePlanController::class, 'datatable'])
                    ->middleware('permission:fee-plans.view')
                    ->name('datatable');

                Route::get('/create', [FeePlanController::class, 'create'])
                    ->middleware('permission:fee-plans.create')
                    ->name('create');

                Route::post('/', [FeePlanController::class, 'store'])
                    ->middleware('permission:fee-plans.create')
                    ->name('store');

                Route::get('/{feePlan}', [FeePlanController::class, 'show'])
                    ->middleware('permission:fee-plans.view')
                    ->name('show');

                Route::get('/{feePlan}/edit', [FeePlanController::class, 'edit'])
                    ->middleware('permission:fee-plans.update')
                    ->name('edit');

                Route::put('/{feePlan}', [FeePlanController::class, 'update'])
                    ->middleware('permission:fee-plans.update')
                    ->name('update');

                Route::delete('/{feePlan}', [FeePlanController::class, 'destroy'])
                    ->middleware('permission:fee-plans.delete')
                    ->name('destroy');
            });

        /*
        |------------------------------------------------------------------
        | إدارة اشتراكات الطلاب
        |------------------------------------------------------------------
        */
        Route::prefix('student-subscriptions')
            ->name('student-subscriptions.')
            ->group(function () {
                Route::get('/', [StudentSubscriptionController::class, 'index'])
                    ->middleware('permission:student-subscriptions.view')
                    ->name('index');

                Route::get('/datatable', [StudentSubscriptionController::class, 'datatable'])
                    ->middleware('permission:student-subscriptions.view')
                    ->name('datatable');

                Route::get('/overdue-datatable', [StudentSubscriptionController::class, 'overdueDatatable'])
                    ->middleware('permission:student-subscriptions.view')
                    ->name('overdue-datatable');

                Route::get('/create', [StudentSubscriptionController::class, 'create'])
                    ->middleware('permission:student-subscriptions.create')
                    ->name('create');

                Route::post('/', [StudentSubscriptionController::class, 'store'])
                    ->middleware('permission:student-subscriptions.create')
                    ->name('store');

                Route::get('/{studentSubscription}', [StudentSubscriptionController::class, 'show'])
                    ->middleware('permission:student-subscriptions.view')
                    ->name('show');

                Route::get('/{studentSubscription}/edit', [StudentSubscriptionController::class, 'edit'])
                    ->middleware('permission:student-subscriptions.update')
                    ->name('edit');

                Route::put('/{studentSubscription}', [StudentSubscriptionController::class, 'update'])
                    ->middleware('permission:student-subscriptions.update')
                    ->name('update');

                Route::delete('/{studentSubscription}', [StudentSubscriptionController::class, 'destroy'])
                    ->middleware('permission:student-subscriptions.delete')
                    ->name('destroy');
            });

        /*
        |------------------------------------------------------------------
        | إدارة المدفوعات وإيصالات القبض
        |------------------------------------------------------------------
        */
        Route::prefix('payments')
            ->name('payments.')
            ->group(function () {
                Route::get('/', [PaymentController::class, 'index'])
                    ->middleware('permission:payments.view')
                    ->name('index');

                Route::get('/datatable', [PaymentController::class, 'datatable'])
                    ->middleware('permission:payments.view')
                    ->name('datatable');

                Route::get('/create', [PaymentController::class, 'create'])
                    ->middleware('permission:payments.create')
                    ->name('create');

                Route::post('/', [PaymentController::class, 'store'])
                    ->middleware('permission:payments.create')
                    ->name('store');

                Route::get('/{payment}', [PaymentController::class, 'show'])
                    ->middleware('permission:payments.view')
                    ->name('show');

                Route::get('/{payment}/edit', [PaymentController::class, 'edit'])
                    ->middleware('permission:payments.update')
                    ->name('edit');

                Route::put('/{payment}', [PaymentController::class, 'update'])
                    ->middleware('permission:payments.update')
                    ->name('update');

                Route::delete('/{payment}', [PaymentController::class, 'destroy'])
                    ->middleware('permission:payments.delete')
                    ->name('destroy');
            });

        // API Routes moved outside this group to avoid strict role middleware

        /*
        |------------------------------------------------------------------
        | إدارة مستحقات المعلمين
        |------------------------------------------------------------------
        */
        Route::prefix('teacher-payrolls')
            ->name('teacher-payrolls.')
            ->group(function () {
                Route::get('/', [TeacherPayrollController::class, 'index'])
                    ->middleware('permission:teacher-payrolls.view')
                    ->name('index');

                Route::get('/datatable', [TeacherPayrollController::class, 'datatable'])
                    ->middleware('permission:teacher-payrolls.view')
                    ->name('datatable');

                Route::get('/create', [TeacherPayrollController::class, 'create'])
                    ->middleware('permission:teacher-payrolls.create')
                    ->name('create');

                Route::post('/', [TeacherPayrollController::class, 'store'])
                    ->middleware('permission:teacher-payrolls.create')
                    ->name('store');

                Route::get('/{teacherPayroll}', [TeacherPayrollController::class, 'show'])
                    ->middleware('permission:teacher-payrolls.view')
                    ->name('show');

                Route::get('/{teacherPayroll}/edit', [TeacherPayrollController::class, 'edit'])
                    ->middleware('permission:teacher-payrolls.update')
                    ->name('edit');

                Route::put('/{teacherPayroll}', [TeacherPayrollController::class, 'update'])
                    ->middleware('permission:teacher-payrolls.update')
                    ->name('update');

                Route::post('/{teacherPayroll}/mark-as-processed', [TeacherPayrollController::class, 'markAsProcessed'])
                    ->middleware('permission:teacher-payrolls.update')
                    ->name('mark-as-processed');
            });

        /*
        |------------------------------------------------------------------
        | إدارة مصروفات التشغيل
        |------------------------------------------------------------------
        */
        Route::prefix('expenses')
            ->name('expenses.')
            ->group(function () {
                Route::get('/', [ExpenseController::class, 'index'])
                    ->middleware('permission:expenses.view')
                    ->name('index');

                Route::get('/datatable', [ExpenseController::class, 'datatable'])
                    ->middleware('permission:expenses.view')
                    ->name('datatable');

                Route::get('/create', [ExpenseController::class, 'create'])
                    ->middleware('permission:expenses.create')
                    ->name('create');

                Route::post('/', [ExpenseController::class, 'store'])
                    ->middleware('permission:expenses.create')
                    ->name('store');

                Route::get('/{expense}', [ExpenseController::class, 'show'])
                    ->middleware('permission:expenses.view')
                    ->name('show');

                Route::get('/{expense}/edit', [ExpenseController::class, 'edit'])
                    ->middleware('permission:expenses.update')
                    ->name('edit');

                Route::put('/{expense}', [ExpenseController::class, 'update'])
                    ->middleware('permission:expenses.update')
                    ->name('update');

                Route::delete('/{expense}', [ExpenseController::class, 'destroy'])
                    ->middleware('permission:expenses.delete')
                    ->name('destroy');
            });

        /*
        |------------------------------------------------------------------
        | الإشعارات
        |------------------------------------------------------------------
        */
        Route::prefix('notifications')
            ->name('notifications.')
            ->group(function () {
                Route::get('/', [AdminNotificationController::class, 'index'])->name('index');
                Route::get('/{notification}', [AdminNotificationController::class, 'show'])->name('show');
                Route::post('/{notification}/read', [AdminNotificationController::class, 'markAsRead'])->name('mark-as-read');
                Route::post('/mark-all-read', [AdminNotificationController::class, 'markAllAsRead'])->name('mark-all-read');
                Route::get('/unread-count', [AdminNotificationController::class, 'unreadCount'])->name('unread-count');
            });

        /*
        |------------------------------------------------------------------
        | التقارير
        |------------------------------------------------------------------
        */
        Route::prefix('reports')
            ->name('reports.')
            ->middleware('permission:reports.view')
            ->group(function () {
                Route::get('/', [ReportController::class, 'index'])->name('index');
                Route::get('/students', [ReportController::class, 'students'])->name('students');
                Route::get('/students/datatable', [ReportController::class, 'studentsDatatable'])->name('students.datatable');
                Route::get('/students/pdf', [ReportController::class, 'studentsPdf'])->name('students.pdf');
                Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance');
                Route::get('/attendance/datatable', [ReportController::class, 'attendanceDatatable'])->name('attendance.datatable');
                Route::get('/attendance/pdf', [ReportController::class, 'attendancePdf'])->name('attendance.pdf');
                Route::get('/progress', [ReportController::class, 'progress'])->name('progress');
                Route::get('/progress/datatable', [ReportController::class, 'progressDatatable'])->name('progress.datatable');
                Route::get('/progress/pdf', [ReportController::class, 'progressPdf'])->name('progress.pdf');
                Route::get('/assessments', [ReportController::class, 'assessments'])->name('assessments');
                Route::get('/assessments/datatable', [ReportController::class, 'assessmentsDatatable'])->name('assessments.datatable');
                Route::get('/assessments/pdf', [ReportController::class, 'assessmentsPdf'])->name('assessments.pdf');
                Route::get('/subscriptions', [ReportController::class, 'subscriptions'])->name('subscriptions');
                Route::get('/subscriptions/datatable', [ReportController::class, 'subscriptionsDatatable'])->name('subscriptions.datatable');
                Route::get('/subscriptions/pdf', [ReportController::class, 'subscriptionsPdf'])->name('subscriptions.pdf');
                Route::get('/payrolls', [ReportController::class, 'payrolls'])->name('payrolls');
                Route::get('/payrolls/datatable', [ReportController::class, 'payrollsDatatable'])->name('payrolls.datatable');
                Route::get('/payrolls/pdf', [ReportController::class, 'payrollsPdf'])->name('payrolls.pdf');
                Route::get('/expenses', [ReportController::class, 'expenses'])->name('expenses');
                Route::get('/expenses/datatable', [ReportController::class, 'expensesDatatable'])->name('expenses.datatable');
                Route::get('/expenses/pdf', [ReportController::class, 'expensesPdf'])->name('expenses.pdf');
            });

        /*
        |------------------------------------------------------------------
        | الإعدادات العامة
        |------------------------------------------------------------------
        */
        Route::prefix('settings')
            ->name('settings.')
            ->middleware('permission:settings.manage')
            ->group(function () {
                Route::get('/', [SettingController::class, 'index'])->name('index');
                Route::post('/', [SettingController::class, 'update'])->name('update');
            });

        /*
        |------------------------------------------------------------------
        | الاستيراد والتصدير
        |------------------------------------------------------------------
        */
        Route::prefix('import-export')
            ->name('import-export.')
            ->middleware('permission:settings.manage')
            ->group(function () {
                Route::get('/', [ImportExportController::class, 'index'])->name('index');
                Route::post('/students/import', [ImportExportController::class, 'importStudents'])->name('students.import');
                Route::get('/students/export', [ImportExportController::class, 'exportStudents'])->name('students.export');
            });

        /*
        |------------------------------------------------------------------
        | النسخ الاحتياطي
        |------------------------------------------------------------------
        */
        Route::prefix('backup')
            ->name('backup.')
            ->middleware('permission:settings.manage')
            ->group(function () {
                Route::get('/', [BackupController::class, 'index'])->name('index');
                Route::post('/', [BackupController::class, 'create'])->name('create');
            });

        /*
        |------------------------------------------------------------------
        | إضافة المعلمين بصفحة مستقلة
        |------------------------------------------------------------------
        */
        Route::prefix('teachers')
            ->name('teachers.')
            ->group(function () {
                Route::get('/create', [TeacherManagementController::class, 'create'])
                    ->middleware('permission:users.create')
                    ->name('create');

                Route::post('/', [TeacherManagementController::class, 'store'])
                    ->middleware('permission:users.create')
                    ->name('store');
            });

        /*
        |------------------------------------------------------------------
        | إدارة المستخدمين
        |------------------------------------------------------------------
        */
        Route::prefix('users')
            ->name('users.')
            ->group(function () {
                Route::get('/', [UserManagementController::class, 'index'])
                    ->middleware('permission:users.view')
                    ->name('index');

                Route::get('/datatable', [UserManagementController::class, 'datatable'])
                    ->middleware('permission:users.view')
                    ->name('datatable');

                Route::get('/create', [UserManagementController::class, 'create'])
                    ->middleware('permission:users.create')
                    ->name('create');

                Route::post('/', [UserManagementController::class, 'store'])
                    ->middleware('permission:users.create')
                    ->name('store');

                Route::get('/{user}', [UserManagementController::class, 'show'])
                    ->middleware('permission:users.view')
                    ->name('show');

                Route::get('/{user}/edit', [UserManagementController::class, 'edit'])
                    ->middleware('permission:users.update')
                    ->name('edit');

                Route::put('/{user}', [UserManagementController::class, 'update'])
                    ->middleware('permission:users.update')
                    ->name('update');

                Route::delete('/{user}', [UserManagementController::class, 'destroy'])
                    ->middleware('permission:users.delete')
                    ->name('destroy');
            });

        // عند بدء أي وحدة تشغيلية جديدة يتم اعتماد نفس أسلوب التنظيم والحماية.

        /*
        |------------------------------------------------------------------
        | API Routes للبيانات الديناميكية
        |------------------------------------------------------------------
        */
        Route::prefix('api')->middleware(['auth', 'verified'])->group(function () {
            Route::get('/student-subscriptions', function (\Illuminate\Http\Request $request) {
                $studentId = $request->input('student_id');
                $subscriptions = \App\Models\StudentSubscription::query()
                    ->where('student_id', $studentId)
                    ->with('feePlan')
                    ->get()
                    ->map(fn ($sub) => [
                        'id'        => $sub->id,
                        'plan_name' => $sub->feePlan?->name ?? '-',
                        'remaining' => $sub->formatted_remaining_amount,
                    ]);

                return response()->json($subscriptions);
            })->name('student-subscriptions');
        });
    });

