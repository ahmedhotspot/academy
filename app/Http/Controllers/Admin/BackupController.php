<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

class BackupController extends AdminController
{
    protected string $title = 'النسخ الاحتياطي';

    public function index(): View
    {
        $backupDir  = storage_path('app/backups');
        $backupFiles = [];

        if (is_dir($backupDir)) {
            $files = glob($backupDir . '/*.sql');
            foreach ($files as $file) {
                $backupFiles[] = [
                    'name' => basename($file),
                    'size' => round(filesize($file) / 1024, 2) . ' KB',
                    'date' => date('Y-m-d H:i:s', filemtime($file)),
                ];
            }
            usort($backupFiles, fn ($a, $b) => strcmp($b['date'], $a['date']));
        }

        return $this->adminView('admin.backup.index', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'النسخ الاحتياطي'],
            ],
            'lastBackup'  => Setting::get('last_backup_at', null),
            'backupFiles' => $backupFiles,
        ]);
    }

    public function create(): RedirectResponse
    {
        try {
            $backupDir = storage_path('app/backups');

            if (! is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $dbPath   = database_path('database.sqlite');
            $filename = 'backup_' . now()->format('Y_m_d_His') . '.sql';
            $dest     = $backupDir . '/' . $filename;

            copy($dbPath, $dest);

            Setting::set('last_backup_at', now()->format('Y-m-d H:i:s'));

            return back()->with('success', "تم إنشاء النسخة الاحتياطية بنجاح: {$filename}");
        } catch (\Exception $e) {
            return back()->withErrors(['backup' => 'فشل النسخ الاحتياطي: ' . $e->getMessage()]);
        }
    }
}

