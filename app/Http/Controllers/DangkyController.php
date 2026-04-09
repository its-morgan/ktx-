<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Enums\RegistrationStatus;
use App\Enums\RegistrationType;
use App\Http\Requests\ApproveRegistrationRequest;
use App\Http\Requests\RequestRoomChangeRequest;
use App\Http\Requests\StoreRegistrationRequest;
use App\Models\Dangky;
use App\Models\Hoadon;
use App\Models\Phong;
use App\Models\Sinhvien;
use App\Services\RegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DangkyController extends Controller
{
    use \App\Traits\KiemtraKyluat;

    private const MESSAGE_ROOM_CONFLICT = 'Phòng đã đầy hoặc đang có người khác đăng ký, vui lòng thử lại.';

    public function __construct(private RegistrationService $registrationService)
    {
    }

    public function storeRegistration(StoreRegistrationRequest $request)
    {
        $dulieu = $request->validated();

        $sinhvien = Sinhvien::where('user_id', Auth::id())->first();
        if (! $sinhvien) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Khong tim thay thong tin sinh vien.');
        }

        $ketQuaKyluat = $this->kiemTraKyluat($sinhvien->id);
        if ($ketQuaKyluat['bi_chan']) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', $ketQuaKyluat['ly_do']);
        }

        try {
            return DB::transaction(function () use ($dulieu, $sinhvien) {
                $sinhvien = Sinhvien::where('id', $sinhvien->id)->lockForUpdate()->first();
                if (! $sinhvien) {
                    return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Khong tim thay thong tin sinh vien.');
                }

                if ($sinhvien->phong_id) {
                    return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Ban da duoc xep phong, khong the dang ky them.');
                }

                $phong = Phong::where('id', (int) $dulieu['phong_id'])->lockForUpdate()->first();
                if (! $phong) {
                    return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Phong khong ton tai.');
                }

                $maxCapacity = $this->getMaxCapacity($phong);
                $currentCount = Sinhvien::where('phong_id', $phong->id)->count();
                if ($currentCount >= $maxCapacity) {
                    return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', self::MESSAGE_ROOM_CONFLICT);
                }

                $dangkyChoDuyet = Dangky::where('sinhvien_id', $sinhvien->id)
                    ->where('trangthai', RegistrationStatus::PENDING->value)
                    ->first();
                if ($dangkyChoDuyet) {
                    return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Ban da gui dang ky, vui long cho admin xu ly.');
                }

                Dangky::create([
                    'sinhvien_id' => $sinhvien->id,
                    'phong_id' => $phong->id,
                    'loaidangky' => RegistrationType::RENTAL->value,
                    'trangthai' => RegistrationStatus::PENDING->value,
                    'ghichu' => null,
                ]);

                return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Gui dang ky phong thanh cong. Vui long cho admin duyet.');
            });
        } catch (\Throwable $e) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Co loi xay ra: '.$e->getMessage());
        }
    }

    public function requestLeaveRoom()
    {
        $sinhvien = Sinhvien::where('user_id', Auth::id())->first();
        if (! $sinhvien || ! $sinhvien->phong_id) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Ban hien khong co phong de tra.');
        }

        $phongDangO = $sinhvien->phong()->first();
        $coHoaDonChuaThanhToan = $phongDangO
            ? $phongDangO->danhsachhoadon()
                ->where('trangthaithanhtoan', InvoiceStatus::PENDING->value)
                ->exists()
            : false;
        if ($coHoaDonChuaThanhToan) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Bạn còn hóa đơn chưa thanh toán. Vui lòng hoàn thành nghĩa vụ tài chính trước khi gửi yêu cầu trả phòng.');
        }

        $dangkyChoDuyet = Dangky::where('sinhvien_id', $sinhvien->id)
            ->where('trangthai', RegistrationStatus::PENDING->value)
            ->where('loaidangky', RegistrationType::RETURN->value)
            ->first();
        if ($dangkyChoDuyet) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Ban da gui yeu cau tra phong, vui long cho admin xu ly.');
        }

        Dangky::create([
            'sinhvien_id' => $sinhvien->id,
            'phong_id' => $sinhvien->phong_id,
            'loaidangky' => RegistrationType::RETURN->value,
            'trangthai' => RegistrationStatus::PENDING->value,
            'ghichu' => null,
        ]);

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Gui yeu cau tra phong thanh cong.');
    }

    public function requestRoomChange(RequestRoomChangeRequest $request)
    {
        $dulieu = $request->validated();

        $sinhvien = Sinhvien::where('user_id', Auth::id())->first();
        if (! $sinhvien || ! $sinhvien->phong_id) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Ban hien khong co phong de doi.');
        }

        $ketQuaKyluat = $this->kiemTraKyluat($sinhvien->id);
        if ($ketQuaKyluat['bi_chan']) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', $ketQuaKyluat['ly_do']);
        }

        if ((int) $sinhvien->phong_id === (int) $dulieu['phong_moi_id']) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Phong moi phai khac phong hien tai.');
        }

        $dangkyChoDuyet = Dangky::where('sinhvien_id', $sinhvien->id)
            ->where('trangthai', RegistrationStatus::PENDING->value)
            ->where('loaidangky', RegistrationType::CHANGE->value)
            ->first();
        if ($dangkyChoDuyet) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Ban da gui yeu cau doi phong, vui long cho admin xu ly.');
        }

        Dangky::create([
            'sinhvien_id' => $sinhvien->id,
            'phong_id' => (int) $dulieu['phong_moi_id'],
            'loaidangky' => RegistrationType::CHANGE->value,
            'trangthai' => RegistrationStatus::PENDING->value,
            'ghichu' => $dulieu['lydo'],
        ]);

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Gui yeu cau doi phong thanh cong. Vui long cho admin duyet.');
    }

    public function listRegistrations(Request $request)
    {
        $status = $request->query('status', '');

        $registrations = Dangky::when($status && ! in_array($status, ['Tất cả'], true), function ($query) use ($status) {
            return $query->where('trangthai', $status);
        })->get();

        $rooms = Phong::all();
        $students = Sinhvien::all();

        return view('admin.dangky.danhsach', [
            'danhsachdangky' => $registrations,
            'danhsachphong' => $rooms,
            'danhsachsinhvien' => $students,
            'status' => $status,
        ]);
    }

    /**
     * Duyet dang ky theo workflow lien thong:
     * sinh vien gui don -> admin duyet -> tao hop dong -> tao hoa don dau tien -> gui email.
     */
    public function approveRegistration(ApproveRegistrationRequest $request, int $id)
    {
        $dulieu = $request->validated();

        try {
            $ketQua = $this->registrationService->duyetDangKy($id, $dulieu['ngay_het_han'] ?? null);

            return redirect()
                ->back()
                ->with('toast_loai', $ketQua['toast_loai'])
                ->with('toast_noidung', $ketQua['toast_noidung']);
        } catch (\Throwable $e) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Co loi xay ra: '.$e->getMessage());
        }
    }

    public function rejectRegistration(Request $request, int $id)
    {
        $dangky = Dangky::find($id);
        if (! $dangky) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Khong tim thay dang ky.');
        }

        $dulieu = $request->validate([
            'ghichu' => ['nullable'],
        ]);

        if (! $dangky->transitionTo(RegistrationStatus::REJECTED->value, $dulieu['ghichu'] ?? null)) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Khong the tu choi don o trang thai hien tai.');
        }

        return redirect()->back()->with('toast_loai', 'thanhcong')->with('toast_noidung', 'Tu choi dang ky thanh cong.');
    }

    private function getMaxCapacity(Phong $phong): int
    {
        $maxCapacity = (int) $phong->succhuamax;

        return max(1, $maxCapacity);
    }
}

