<?php

namespace App\Observers;

use App\Models\Phong;
use App\Models\Sinhvien;

class SinhvienObserver
{
    /**
     * Handle the Sinhvien "created" event.
     */
    public function created(Sinhvien $sinhvien): void
    {
        if ($sinhvien->phong_id) {
            $this->syncRoomOccupancy((int) $sinhvien->phong_id);
        }
    }

    /**
     * Handle the Sinhvien "updated" event.
     */
    public function updated(Sinhvien $sinhvien): void
    {
        if ($sinhvien->wasChanged('phong_id')) {
            $oldRoomId = $sinhvien->getOriginal('phong_id');
            $newRoomId = $sinhvien->phong_id;

            if ($oldRoomId) {
                $this->syncRoomOccupancy((int) $oldRoomId);
            }

            if ($newRoomId) {
                $this->syncRoomOccupancy((int) $newRoomId);
            }
        }
    }

    /**
     * Handle the Sinhvien "deleted" event.
     */
    public function deleted(Sinhvien $sinhvien): void
    {
        $phongId = $sinhvien->phong_id ?? $sinhvien->getOriginal('phong_id');
        if ($phongId) {
            $this->syncRoomOccupancy((int) $phongId);
        }
    }

    /**
     * Synchronize actual occupancy count (dango) for a specific room.
     */
    private function syncRoomOccupancy(int $roomId): void
    {
        if ($roomId <= 0) {
            return;
        }

        $occupancy = Sinhvien::where('phong_id', $roomId)->count();
        Phong::where('id', $roomId)->update(['dango' => $occupancy]);
    }
}
