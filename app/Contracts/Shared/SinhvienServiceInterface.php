<?php

namespace App\Contracts\Shared;

use Illuminate\Http\Request;

interface SinhvienServiceInterface
{
    /**
     * Lấy danh sách sinh viên.
     */
    public function lietKeSinhVien(Request $request): array;

    /**
     * Cập nhật thông tin sinh viên.
     */
    public function capNhatSinhVien(int $id, array $data): array;

    /**
     * Xếp phòng cho sinh viên.
     */
    public function xepPhong(int $id, ?int $phongId): array;

    /**
     * Cho sinh viên rời phòng.
     */
    public function choRoiOPhong(int $id): array;
}
