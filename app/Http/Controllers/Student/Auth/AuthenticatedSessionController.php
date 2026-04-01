<?php

namespace App\Http\Controllers\Student\Auth;

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
        return view('student.auth.login');
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

        if (! Auth::guard('student')->attempt(
            ['phone' => $request->phone, 'password' => $request->password],
            $request->boolean('remember')
        )) {
            throw ValidationException::withMessages([
                'phone' => 'رقم الهاتف أو كلمة المرور غير صحيحة.',
            ]);
        }

        $student = Auth::guard('student')->user();

        if ($student->status !== 'active') {
            Auth::guard('student')->logout();
            throw ValidationException::withMessages([
                'phone' => 'حسابك غير نشط. تواصل مع الإدارة.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->route('student.dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('student')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login');
    }
}

