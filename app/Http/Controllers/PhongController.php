<?php

namespace App\Http\Controllers;

use App\Interfaces\PhongAssetServiceInterface;
use App\Interfaces\PhongServiceInterface;
use App\Interfaces\PhongSupplyServiceInterface;
use Illuminate\Http\Request;

class PhongController extends Controller
{
    public function __construct(
        private readonly PhongServiceInterface $phongService,
        private readonly PhongAssetServiceInterface $assetService,
        private readonly PhongSupplyServiceInterface $supplyService,
    ) {}

    /**
     * PUBLIC ROUTE: Display list of rooms for visitors (no login required).
     * - Group rooms by floor
     * - Display available slots
     */
    public function listRoomsPublic(Request $request)
    {
        $data = $this->phongService->listRoomsPublic($request);
        return view('public.phong.danhsach', $data);
    }

    /**
     * PUBLIC ROUTE: View details of room assets.
     */
    public function viewRoomAssetsPublic(int $id)
    {
        return $this->assetService->getPublic($id);
    }

    /**
     * Display available rooms for students.
     */
    public function listStudentRooms(Request $request)
    {
        $data = $this->phongService->listStudentRooms($request);
        return view('student.phong.danhsach', [
            'danhsachphong' => $data['danhsachphongtrong'],
            'soluongdango_theophong' => $data['soluongdango_theophong'],
            'tuKhoa' => $data['tuKhoa'],
        ]);
    }

    /**
     * Chức năng sinh viên: xem tài sản phòng đang ở.
     */
    public function studentAssets()
    {
        return $this->assetService->getByStudent();
    }

    /**
     * Danh sách phòng cho admin (quản trị).
     */
    public function listRooms(Request $request)
    {
        $data = $this->phongService->listRooms($request);
        return view('admin.phong.danhsach', $data);
    }

    /**
     * Chi tiết phòng (admin) bao gồm tài sản.
     */
    public function viewRoom(int $id)
    {
        $data = $this->phongService->viewRoom($id);

        if (isset($data['error'])) {
            return redirect()->back()
                ->with('toast_loai', 'loi')
                ->with('toast_noidung', $data['error']);
        }

        return view('admin.phong.chitiet', $data);
    }

    /**
     * Thêm tài sản vào phòng (admin).
     */
    public function storeAsset(Request $request, int $id)
    {
        return $this->assetService->store($request, $id);
    }

    /**
     * Cập nhật tài sản (admin).
     */
    public function updateAsset(Request $request, int $id, int $taisanId)
    {
        return $this->assetService->update($request, $id, $taisanId);
    }

    /**
     * Xóa tài sản (admin).
     */
    public function destroyAsset(int $id, int $taisanId)
    {
        return $this->assetService->destroy($id, $taisanId);
    }

    /**
     * Thêm vật tư vào phòng (admin).
     */
    public function storeSupply(Request $request, int $id)
    {
        return $this->supplyService->store($request, $id);
    }

    /**
     * Cập nhật vật tư (admin).
     */
    public function updateSupply(Request $request, int $id, int $vattuId)
    {
        return $this->supplyService->update($request, $id, $vattuId);
    }

    /**
     * Xóa vật tư (admin).
     */
    public function destroySupply(int $id, int $vattuId)
    {
        return $this->supplyService->destroy($id, $vattuId);
    }

    /**
     * Thêm mới phòng (admin).
     */
    public function storeRoom(Request $request)
    {
        $result = $this->phongService->storeRoom($request);

        return redirect()->back()
            ->with('toast_loai', $result['success'] ? 'thanhcong' : 'loi')
            ->with('toast_noidung', $result['message']);
    }

    /**
     * Cập nhật phòng (admin).
     */
    public function updateRoom(Request $request, int $id)
    {
        $result = $this->phongService->updateRoom($request, $id);

        return redirect()->back()
            ->with('toast_loai', $result['success'] ? 'thanhcong' : 'loi')
            ->with('toast_noidung', $result['message']);
    }

    /**
     * Xóa phòng (admin).
     */
    public function destroyRoom(int $id)
    {
        $result = $this->phongService->destroyRoom($id);

        return redirect()->back()
            ->with('toast_loai', $result['success'] ? 'thanhcong' : 'loi')
            ->with('toast_noidung', $result['message']);
    }
}
