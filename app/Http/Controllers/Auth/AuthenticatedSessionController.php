<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        /**
         * Hàm này dùng để redirect sau đăng nhập theo vai trò.
         * - Vai trò lấy từ: Auth::user()->vaitro (bảng users, cột vaitro)
         * - Nếu admin: chuyển về /admin/trangchu
         * - Nếu sinhvien: chuyển về /student/trangchu
         */
        $vaitro = Auth::user()->vaitro ?? 'sinhvien';

        if ($vaitro === 'admin') {
            return redirect()->intended(route('admin.trangchu', absolute: false));
        }

        return redirect()->intended(route('student.trangchu', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
