<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Settings\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends AdminController
{
    protected string $title = 'الإعدادات العامة';

    public function index(): View
    {
        return $this->adminView('admin.settings.index', [
            'breadcrumbs' => [
                ['title' => 'الرئيسية', 'url' => route('admin.dashboard')],
                ['title' => 'الإعدادات العامة'],
            ],
            'settings' => [
                'institution_name'    => Setting::get('institution_name', 'أكاديمية القرآن الكريم'),
                'institution_address' => Setting::get('institution_address', ''),
                'institution_phone'   => Setting::get('institution_phone', ''),
                'institution_email'   => Setting::get('institution_email', ''),
                'institution_logo'    => Setting::get('institution_logo', ''),
                'last_backup_at'      => Setting::get('last_backup_at', null),
            ],
        ]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('institution_logo');

        Setting::setMany($data);

        if ($request->hasFile('institution_logo')) {
            $path = $request->file('institution_logo')->store('logos', 'public');
            Setting::set('institution_logo', asset('storage/' . $path));
        }

        return back()->with('success', 'تم حفظ الإعدادات بنجاح.');
    }
}
