<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hàm này dùng để chuyển các giá trị trạng thái cũ (không dấu) sang tiếng Việt có dấu.
     * - Dữ liệu lấy từ các bảng: dangky, hoadon, baohong
     * - Mục tiêu: đồng bộ trạng thái lưu trong DB để hiển thị “dễ bảo vệ”
     */
    public function up(): void
    {
        // Chuyển trạng thái đăng ký phòng
        DB::table('dangky')->where('trangthai', 'choxuly')->update(['trangthai' => 'Chờ xử lý']);
        DB::table('dangky')->where('trangthai', 'duyet')->update(['trangthai' => 'Đã duyệt']);
        DB::table('dangky')->where('trangthai', 'tuchoi')->update(['trangthai' => 'Từ chối']);

        // Chuyển trạng thái thanh toán hóa đơn
        DB::table('hoadon')->where('trangthaithanhtoan', 'chuathanhtoan')->update(['trangthaithanhtoan' => 'Chưa thanh toán']);
        DB::table('hoadon')->where('trangthaithanhtoan', 'dathanhtoan')->update(['trangthaithanhtoan' => 'Đã thanh toán']);

        // Chuyển trạng thái báo hỏng
        DB::table('baohong')->where('trangthai', 'chosua')->update(['trangthai' => 'Chờ sửa']);
        DB::table('baohong')->where('trangthai', 'daxong')->update(['trangthai' => 'Đã xong']);

        // Đổi default trong MySQL để về sau insert không bị quay lại giá trị cũ
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `dangky` MODIFY `trangthai` VARCHAR(255) NOT NULL DEFAULT 'Chờ xử lý'");
            DB::statement("ALTER TABLE `hoadon` MODIFY `trangthaithanhtoan` VARCHAR(255) NOT NULL DEFAULT 'Chưa thanh toán'");
            DB::statement("ALTER TABLE `baohong` MODIFY `trangthai` VARCHAR(255) NOT NULL DEFAULT 'Chờ sửa'");
        }
    }

    /**
     * Hàm này hoàn tác về giá trị cũ (không dấu).
     * - Dùng khi rollback migration
     */
    public function down(): void
    {
        DB::table('dangky')->where('trangthai', 'Chờ xử lý')->update(['trangthai' => 'choxuly']);
        DB::table('dangky')->where('trangthai', 'Đã duyệt')->update(['trangthai' => 'duyet']);
        DB::table('dangky')->where('trangthai', 'Từ chối')->update(['trangthai' => 'tuchoi']);

        DB::table('hoadon')->where('trangthaithanhtoan', 'Chưa thanh toán')->update(['trangthaithanhtoan' => 'chuathanhtoan']);
        DB::table('hoadon')->where('trangthaithanhtoan', 'Đã thanh toán')->update(['trangthaithanhtoan' => 'dathanhtoan']);

        DB::table('baohong')->where('trangthai', 'Chờ sửa')->update(['trangthai' => 'chosua']);
        DB::table('baohong')->where('trangthai', 'Đã xong')->update(['trangthai' => 'daxong']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `dangky` MODIFY `trangthai` VARCHAR(255) NOT NULL DEFAULT 'choxuly'");
            DB::statement("ALTER TABLE `hoadon` MODIFY `trangthaithanhtoan` VARCHAR(255) NOT NULL DEFAULT 'chuathanhtoan'");
            DB::statement("ALTER TABLE `baohong` MODIFY `trangthai` VARCHAR(255) NOT NULL DEFAULT 'chosua'");
        }
    }
};
