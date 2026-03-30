<?php

namespace App\Http\Controllers\Admin\_stubs;

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * ======================================================
 * CRUD Controller Skeleton — نموذج موحّد
 * ======================================================
 * انسخ هذا الملف وعدّل عليه لكل وحدة جديدة.
 *
 * 1. غيّر اسم الـ class
 * 2. غيّر $title و $viewPath
 * 3. استبدل Action و Request بالصحيح
 * 4. عدّل route('admin.MODULE.*')
 */

// use App\Actions\MODULE\CreateMODULEAction;
// use App\Actions\MODULE\UpdateMODULEAction;
// use App\Actions\MODULE\DeleteMODULEAction;
// use App\Http\Requests\Admin\MODULE\StoreMODULERequest;
// use App\Http\Requests\Admin\MODULE\UpdateMODULERequest;
// use App\Models\MODULE;

class CrudControllerSkeleton extends AdminController
{
    protected string $title    = 'اسم الوحدة';  // عنوان الصفحة بالعربي
    protected string $viewPath = 'admin.MODULE'; // مسار الـ views

    // ─────────────────────────────────────────────
    // قائمة السجلات — DataTable Ajax
    // ─────────────────────────────────────────────
    public function index(): View
    {
        return $this->adminView("{$this->viewPath}.index");
    }

    // DataTable endpoint — Ajax فقط
    public function datatable(Request $request)
    {
        // $records = MODULE::query()
        //     ->when(auth()->user()->branch_id, fn($q) => $q->where('branch_id', auth()->user()->branch_id))
        //     ->select(...);
        //
        // return DataTables::of($records)->make(true);
    }

    // ─────────────────────────────────────────────
    // إضافة سجل جديد
    // ─────────────────────────────────────────────
    public function create(): View
    {
        return $this->adminView("{$this->viewPath}.create");
    }

    public function store(/* StoreMODULERequest */ $request): RedirectResponse
    {
        // (new CreateMODULEAction())->handle($request->validated());
        return redirect()->route('admin.MODULE.index')
            ->with('success', 'تمت الإضافة بنجاح.');
    }

    // ─────────────────────────────────────────────
    // عرض سجل
    // ─────────────────────────────────────────────
    public function show(/* MODULE */ $record): View
    {
        return $this->adminView("{$this->viewPath}.show", compact('record'));
    }

    // ─────────────────────────────────────────────
    // تعديل سجل
    // ─────────────────────────────────────────────
    public function edit(/* MODULE */ $record): View
    {
        return $this->adminView("{$this->viewPath}.edit", compact('record'));
    }

    public function update(/* UpdateMODULERequest */ $request, /* MODULE */ $record): RedirectResponse
    {
        // (new UpdateMODULEAction())->handle($request->validated(), $record);
        return redirect()->route('admin.MODULE.index')
            ->with('success', 'تم التحديث بنجاح.');
    }

    // ─────────────────────────────────────────────
    // حذف سجل
    // ─────────────────────────────────────────────
    public function destroy(/* MODULE */ $record): RedirectResponse
    {
        // (new DeleteMODULEAction())->handle($record);
        return redirect()->route('admin.MODULE.index')
            ->with('success', 'تم الحذف بنجاح.');
    }
}

