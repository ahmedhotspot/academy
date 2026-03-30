<?php

namespace App\Http\Controllers\Admin;

use App\Exports\StudentsExport;
use App\Imports\StudentsImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ImportExportController extends AdminController
{
    protected string $title = 'الاستيراد والتصدير';

    public function index(): View
    {
        return $this->adminView('admin.import-export.index', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'الاستيراد والتصدير'],
            ],
        ]);
    }

    public function importStudents(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ], [
            'file.required' => 'يجب اختيار ملف',
            'file.mimes'    => 'يجب أن يكون الملف من نوع Excel أو CSV',
            'file.max'      => 'حجم الملف يتجاوز الحد المسموح',
        ]);

        try {
            Excel::import(new StudentsImport(), $request->file('file'));

            return back()->with('success', 'تم استيراد بيانات الطلاب بنجاح.');
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'حدث خطأ أثناء الاستيراد: ' . $e->getMessage()]);
        }
    }

    public function exportStudents(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new StudentsExport(), 'الطلاب_' . now()->format('Y-m-d') . '.xlsx');
    }
}

