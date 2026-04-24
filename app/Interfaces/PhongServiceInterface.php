<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface PhongServiceInterface
{
    /**
     * Danh sách phòng cho admin.
     */
    public function listRooms(Request $request): array;

    /**
     * Danh sách phòng công khai (khách chưa đăng nhập).
     */
    public function listRoomsPublic(Request $request): array;

    /**
     * Danh sách phòng cho sinh viên đăng nhập chọn.
     */
    public function listStudentRooms(Request $request): array;

    /**
     * Chi tiết phòng (admin).
     *
     * @return array ['phong' => Phong, 'taisan' => Collection, 'vattu' => Collection] | ['error' => string]
     */
    public function viewRoom(int $id): array;

    /**
     * Thêm phòng mới (admin).
     */
    public function storeRoom(Request $request): array;

    /**
     * Cập nhật phòng (admin).
     */
    public function updateRoom(Request $request, int $id): array;

    /**
     * Xóa phòng (admin).
     */
    public function destroyRoom(int $id): array;
}
