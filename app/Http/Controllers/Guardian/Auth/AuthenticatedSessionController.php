<?php

namespace App\Http\Controllers\Guardian\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('guardian.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'phone'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'phone.required'    => 'رقم الهاتف مطلوب.',
            'password.required' => 'كلمة المرور مطلوبة.',
        ]);

        if (! Auth::guard('guardian')->attempt(
            ['phone' => $request->phone, 'password' => $request->password],
            $request->boolean('remember')
        )) {
            throw ValidationException::withMessages([
                'phone' => 'رقم الهاتف أو كلمة المرور غير صحيحة.',
            ]);
        }

        $guardian = Auth::guard('guardian')->user();

        if ($guardian->status !== 'active') {
            Auth::guard('guardian')->logout();
            throw ValidationException::withMessages([
                'phone' => 'حسابك غير نشط. تواصل مع الإدارة.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->route('guardian.dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('guardian')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('guardian.login');
    }
}

