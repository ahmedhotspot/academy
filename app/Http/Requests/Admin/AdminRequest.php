<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * AdminRequest — Base لجميع Form Requests داخل لوحة التحكم
 *
 * كل Request في Admin يجب أن يرث من هذا الـ Class.
 * يوفّر:
 * - تحديد الصلاحية authorize() افتراضيًا للمستخدم المسجل
 * - رسائل خطأ عربية موحّدة
 * - إعادة التوجيه مع الأخطاء عند فشل الـ validation
 */
abstract class AdminRequest extends FormRequest
{
    /**
     * تحديد من له صلاحية تنفيذ هذا الطلب
     * الافتراضي: أي مستخدم مسجل دخوله
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * رسائل الخطأ العربية الافتراضية
     * يمكن تجاوزها في كل Request
     */
    public function messages(): array
    {
        return [
            'required'  => 'حقل :attribute إلزامي.',
            'string'    => 'حقل :attribute يجب أن يكون نصًا.',
            'max'       => 'حقل :attribute يجب ألا يتجاوز :max حرفًا.',
            'min'       => 'حقل :attribute يجب أن يكون على الأقل :min أحرف.',
            'email'     => 'حقل :attribute يجب أن يكون بريدًا إلكترونيًا صحيحًا.',
            'unique'    => 'قيمة :attribute مستخدمة بالفعل.',
            'exists'    => 'القيمة المحددة في :attribute غير موجودة.',
            'numeric'   => 'حقل :attribute يجب أن يكون رقمًا.',
            'integer'   => 'حقل :attribute يجب أن يكون عددًا صحيحًا.',
            'date'      => 'حقل :attribute يجب أن يكون تاريخًا صحيحًا.',
            'in'        => 'القيمة المختارة في :attribute غير صحيحة.',
            'confirmed' => 'حقل :attribute غير متطابق مع التأكيد.',
            'image'     => 'حقل :attribute يجب أن يكون صورة.',
            'mimes'     => 'حقل :attribute يجب أن يكون من نوع: :values.',
            'nullable'  => 'حقل :attribute يمكن تركه فارغًا.',
            'boolean'   => 'حقل :attribute يجب أن يكون صح أو خطأ.',
            'array'     => 'حقل :attribute يجب أن يكون مصفوفة.',
            'gt.numeric'=> 'حقل :attribute يجب أن يكون أكبر من :value.',
            'gte.numeric'=> 'حقل :attribute يجب أن يكون أكبر من أو يساوي :value.',
        ];
    }
}

