<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Kiemtravaitro
{
    /**
     * Hàm này dùng để kiểm tra vai trò (vaitro) của người đăng nhập.
     * - $vaitrobatbuoc: lấy từ tham số middleware trên route, ví dụ: ->middleware('kiemtravaitro:admin')
     * - Auth::user(): lấy từ session đăng nhập (Breeze)
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $vaitrobatbuoc): Response
    {
        // Nếu chưa đăng nhập thì để middleware auth xử lý trước
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        // Lấy vai trò của người dùng đang đăng nhập từ bảng users (cột vaitro)
        $vaitrohientai = Auth::user()->vaitro ?? null;

        // Nếu sai vai trò thì quay về dashboard và báo lỗi tiếng Việt
        if ($vaitrohientai !== $vaitrobatbuoc) {
            return redirect()
                ->route('dashboard')
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Bạn không có quyền truy cập chức năng này.');
        }

        return $next($request);
    }
}
