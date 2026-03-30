<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * الترتيب مهم:
     *   1. branches  — يجب أن يُنشأ قبل users (FK dependency)
     *   2. users
     *   3. password_reset_tokens
     *   4. sessions
     */
    public function up(): void
    {
        // =====================================================
        // 1. جدول الفروع (branches)
        // =====================================================
        Schema::create('branches', function (Blueprint $table) {
            $table->id();

            // البيانات الأساسية
            $table->string('name');                          // اسم الفرع
            $table->string('code', 20)->unique();            // كود مميز للفرع (مثال: RYD-01)

            // بيانات التواصل
            $table->string('phone', 20)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('email')->nullable();

            // الموقع
            $table->string('city', 100)->nullable();
            $table->text('address')->nullable();

            // الإدارة
            $table->string('manager_name')->nullable();      // اسم مسؤول الفرع

            // ملاحظات وحالة
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive'])
                  ->default('active')
                  ->comment('active = نشط | inactive = غير نشط');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index('city');
        });

        // =====================================================
        // 2. جدول المستخدمين (users)
        // =====================================================
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // الفرع — nullable لأن المشرف العام لا ينتمي لفرع بعينه
            $table->foreignId('branch_id')
                  ->nullable()
                  ->constrained('branches')
                  ->nullOnDelete()
                  ->comment('null = مشرف عام | له قيمة = مرتبط بفرع');

            // البيانات الشخصية
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('phone', 20)->comment('رقم الجوال — مطلوب');
            $table->string('username', 50)->nullable()->unique()
                  ->comment('اسم المستخدم للدخول — اختياري');

            // المصادقة
            $table->string('password');
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();

            // بيانات إضافية
            $table->string('avatar')->nullable()
                  ->comment('مسار الصورة الشخصية داخل storage');

            // الحالة
            $table->enum('status', ['active', 'inactive', 'suspended'])
                  ->default('active')
                  ->comment('active = نشط | inactive = غير نشط | suspended = موقوف');

            // متابعة آخر دخول
            $table->timestamp('last_login_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('branch_id');
            $table->index('status');
            $table->index('phone');
            $table->index('last_login_at');
        });

        // =====================================================
        // 3. جدول رموز إعادة كلمة المرور
        // =====================================================
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();

            $table->index('created_at');
        });

        // =====================================================
        // 4. جدول الجلسات (Sessions)
        // =====================================================
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     * الترتيب عكسي لاحترام FK constraints
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('branches');
    }
};
