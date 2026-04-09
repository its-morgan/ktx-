<?php

namespace App\Http\Controllers;

use App\Models\Lienhe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        return view('landing.index');
    }

    public function guiLienHe(Request $request): RedirectResponse
    {
        $duLieu = $request->validate(
            [
                'ho_ten' => ['required', 'string', 'max:100'],
                'email' => ['required', 'email', 'max:150'],
                'noi_dung' => ['required', 'string', 'max:2000'],
            ],
            [
                'ho_ten.required' => 'Vui lòng nhập họ và tên.',
                'email.required' => 'Vui lòng nhập email liên hệ.',
                'email.email' => 'Email không đúng định dạng.',
                'noi_dung.required' => 'Vui lòng nhập nội dung câu hỏi.',
            ]
        );

        Lienhe::create($duLieu);

        return redirect()
            ->to(route('home').'#lien-he')
            ->with('lienhe_thanhcong', 'Cảm ơn bạn đã liên hệ. Ban quản lý sẽ phản hồi sớm.');
    }
}

