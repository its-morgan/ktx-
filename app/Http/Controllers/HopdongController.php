<?php

namespace App\Http\Controllers;

use App\Enums\ContractStatus;
use App\Enums\DisciplineLevel;
use App\Http\Requests\ExtendContractRequest;
use App\Http\Requests\StoreContractRequest;
use App\Models\Hopdong;
use App\Models\Phong;
use App\Models\Sinhvien;
use App\Services\ContractService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HopdongController extends Controller
{
    use \App\Traits\KiemtraKyluat;

    public function __construct(private ContractService $contractService)
    {
    }

    public function listContracts(Request $request)
    {
        $status = $request->query('trangthai', 'Tất cả');
        $search = $request->query('timkiem', '');

        $contracts = Hopdong::with(['sinhvien.taikhoan', 'phong'])
            ->when($status && $status !== 'Tất cả', function ($query) use ($status) {
                return $query->where('trang_thai', $status);
            })
            ->when($search, function ($query) use ($search) {
                return $query->whereHas('sinhvien', function ($q) use ($search) {
                    $q->where('masinhvien', 'like', "%{$search}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(20);

        $studentsWithoutRoom = Sinhvien::whereNull('phong_id')->get();
        $rooms = Phong::all();

        return view('admin.hopdong.danhsach', [
            'danhsachhopdong' => $contracts,
            'trangthai' => $status,
            'timkiem' => $search,
            'sinhvienChuaCoPhong' => $studentsWithoutRoom,
            'danhsachphong' => $rooms,
        ]);
    }

    /**
     * Tạo hợp đồng thủ công cho admin.
     * - Kiểm tra sinh viên chưa có hợp đồng đang hiệu lực ở phòng khác.
     * - Kiểm tra phòng còn chỗ trống.
     * - Sử dụng DB::transaction để đảm bảo toàn vẹn dữ liệu.
     */
    public function store(StoreContractRequest $request)
    {
        $dulieu = $request->validated();

        $result = $this->contractService->createContract($dulieu);

        return redirect()
            ->back()
            ->with('toast_loai', $result['success'] ? 'thanhcong' : 'loi')
            ->with('toast_noidung', $result['message']);
    }

    public function extend(ExtendContractRequest $request, int $id)
    {
        $hopdong = Hopdong::find($id);

        if (! $hopdong) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Không tìm thấy hợp đồng.');
        }

        // Kiểm tra kỷ luật trước khi gia hạn
        $ketQuaKyluat = $this->kiemTraKyluat($hopdong->sinhvien_id);
        if ($ketQuaKyluat['bi_chan']) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', $ketQuaKyluat['ly_do']);
        }

        $dulieu = $request->validated();
        $currentEndDate = (string) $hopdong->ngay_ket_thuc;

        $result = $this->contractService->extendContract($id, $dulieu['ngay_ket_thuc'], $currentEndDate);

        return redirect()
            ->back()
            ->with('toast_loai', $result['success'] ? 'thanhcong' : 'loi')
            ->with('toast_noidung', $result['message']);
    }

    public function close(int $id)
    {
        $result = $this->contractService->closeContract($id);

        return redirect()
            ->back()
            ->with('toast_loai', $result['success'] ? 'thanhcong' : 'loi')
            ->with('toast_noidung', $result['message']);
    }

    private function syncOccupancy(array $roomIds): void
    {
        $validRoomIds = array_unique(
            array_filter(
                array_map(static fn ($id) => (int) $id, $roomIds),
                static fn (int $id) => $id > 0
            )
        );

        foreach ($validRoomIds as $roomId) {
            $occupancy = Sinhvien::where('phong_id', $roomId)->count();
            Phong::where('id', $roomId)->update(['dango' => $occupancy]);
        }
    }

    public function myContracts(Request $request)
    {
        $sinhvien = Sinhvien::where('user_id', auth()->id())->first();

        if (! $sinhvien) {
            return redirect()->back()->with('toast_loai', 'loi')->with('toast_noidung', 'Không tìm thấy thông tin sinh viên.');
        }

        $contracts = $sinhvien->danhsachhopdong()->with('phong')->orderBy('id', 'desc')->get();

        return view('student.hopdong.index', [
            'danhsachhopdong' => $contracts,
        ]);
    }

    /**
     * Xuất hợp đồng PDF.
     * - Sử dụng Barryvdh\DomPDF\Facade\Pdf (cần cài đặt package)
     */
    public function downloadPDF(int $id)
    {
        $hopdong = Hopdong::with(['sinhvien.taikhoan', 'phong'])->find($id);

        if (! $hopdong) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Không tìm thấy hợp đồng.');
        }

        // Nếu chưa cài DomPDF, trả về thông báo
        if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return redirect()
                ->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', 'Vui lòng cài đặt package barryvdh/laravel-dompdf: composer require barryvdh/laravel-dompdf');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.hopdong', [
            'hopdong' => $hopdong,
            'sinhvien' => $hopdong->sinhvien,
            'phong' => $hopdong->phong,
        ]);

        return $pdf->download('hopdong_' . $hopdong->sinhvien->masinhvien . '_' . $hopdong->ngay_bat_dau . '.pdf');
    }
}
