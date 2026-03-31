<?php

namespace App\Console\Commands;

use App\Models\Hopdong;
use App\Models\Phong;
use App\Models\Sinhvien;
use Illuminate\Console\Command;

class DongBoHopDong extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dongbo:hopdong';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ dữ liệu ngày vào/ngày hết hạn từ bảng sinhvien sang hopdong';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Bắt đầu đồng bộ hợp đồng từ sinhvien...');

        $sinhs = Sinhvien::with('phong')->whereNotNull('phong_id')->whereNotNull('ngay_vao')->whereNotNull('ngay_het_han')->get();

        foreach ($sinhs as $sinhvien) {
            if (! $sinhvien->phong) {
                $this->warn("Sinh viên {$sinhvien->id} không có phòng, bỏ qua.");
                continue;
            }

            $hopdong = Hopdong::firstOrNew([
                'sinhvien_id' => $sinhvien->id,
                'phong_id' => $sinhvien->phong_id,
                'ngay_bat_dau' => $sinhvien->ngay_vao,
                'ngay_ket_thuc' => $sinhvien->ngay_het_han,
            ]);

            $hopdong->giaphong_luc_ky = (int) ($sinhvien->phong->giaphong ?? 0);
            $hopdong->trang_thai = 'Đang hiệu lực';
            $hopdong->ghichu = $hopdong->ghichu ?? null;
            $hopdong->save();

            $this->info("Đồng bộ hợp đồng cho SV {$sinhvien->id} thành công.");
        }

        $this->info('Đã hoàn thành đồng bộ hợp đồng.');

        return Command::SUCCESS;
    }
}
