<?php

namespace App\Observers;

use App\Models\Phong;
use App\Models\Sinhvien;

class StudentObserver
{
    /**
     * Handle the Sinhvien "updated" event.
     * Sync occupancy count when student room assignment changes.
     */
    public function updated(Sinhvien $sinhvien): void
    {
        // Check if phong_id was changed
        if ($sinhvien->isDirty('phong_id')) {
            $oldRoomId = $sinhvien->getOriginal('phong_id');
            $newRoomId = $sinhvien->phong_id;

            // Sync old room occupancy
            if ($oldRoomId) {
                $this->syncRoomOccupancy((int) $oldRoomId);
            }

            // Sync new room occupancy
            if ($newRoomId) {
                $this->syncRoomOccupancy((int) $newRoomId);
            }
        }
    }

    /**
     * Handle the Sinhvien "created" event.
     * If created with a room_id, sync that room's occupancy.
     */
    public function created(Sinhvien $sinhvien): void
    {
        if ($sinhvien->phong_id) {
            $this->syncRoomOccupancy((int) $sinhvien->phong_id);
        }
    }

    /**
     * Handle the Sinhvien "deleted" event.
     * Sync occupancy when student is deleted.
     */
    public function deleted(Sinhvien $sinhvien): void
    {
        if ($sinhvien->phong_id) {
            $this->syncRoomOccupancy((int) $sinhvien->phong_id);
        }
    }

    /**
     * Synchronize occupancy count (dango) for a specific room.
     * 
     * @param int $roomId
     * @return void
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
