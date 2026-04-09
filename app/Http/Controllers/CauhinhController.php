<?php

namespace App\Http\Controllers;

use App\Models\Cauhinh;
use Illuminate\Http\Request;

class CauhinhController extends Controller
{
    /**
     * Display system configuration for admin.
     * - Get data from: cauhinh table
     */
    public function showSettings()
    {
        $cauhinh = Cauhinh::all()->keyBy('ten');

        return view('admin.cauhinh.index', compact('cauhinh'));
    }

    /**
     * Update system configuration (electricity_price, water_price, hotline).
     * - Form data: gia_dien, gia_nuoc, hotline
     */
    public function updateSettings(Request $request)
    {
        $dulieu = $request->validate([
            'gia_dien' => ['required', 'numeric', 'min:0'],
            'gia_nuoc' => ['required', 'numeric', 'min:0'],
            'hotline' => ['required', 'string'],
        ]);

        foreach (['gia_dien', 'gia_nuoc', 'hotline'] as $key) {
            Cauhinh::updateOrCreate(['ten' => $key], ['giatri' => (string) $dulieu[$key]]);
        }

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Cấu hình cập nhật thành công.');
    }
}
