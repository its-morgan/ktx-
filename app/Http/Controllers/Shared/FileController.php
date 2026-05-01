<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Dangky;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    /**
     * Show private file (CCCD, etc.) via signed URL or admin auth.
     */
    public function showPrivateFile(Request $request, string $path): StreamedResponse
    {
        $nguoiDung = $request->user();
        if (! $nguoiDung) {
            abort(403);
        }

        $laAdmin = $nguoiDung->hasAnyRole([
            \App\Models\User::ROLE_ADMIN,
            \App\Models\User::ROLE_ADMIN_TRUONG,
            \App\Models\User::ROLE_ADMIN_TOA_NHA,
            \App\Models\User::ROLE_LE_TAN,
        ]);

        if (! $laAdmin) {
            $coChuKyHopLe = URL::hasValidSignature($request);
            $duocSoHuu = $this->nguoiDungSoHuuFile($nguoiDung->id, $path);

            if (! $coChuKyHopLe && ! $duocSoHuu) {
                abort(403, 'Bạn không có quyền truy cập tệp này.');
            }
        }

        if (! Storage::disk('private')->exists($path)) {
            abort(404);
        }

        return Storage::disk('private')->response($path);
    }

    /**
     * Generate signed URL for private file access
     */
    public static function generateSignedUrl(string $path, int $expiresInMinutes = 60): string
    {
        return URL::signedRoute('private.file', ['path' => $path], now()->addMinutes($expiresInMinutes));
    }

    private function nguoiDungSoHuuFile(int $userId, string $path): bool
    {
        $banGhi = Dangky::query()
            ->whereHas('sinhvien', fn ($query) => $query->where('user_id', $userId))
            ->where(function ($query) use ($path) {
                $query->where('anh_the_path', $path)
                    ->orWhere('anh_cccd_path', $path);
            })
            ->exists();

        return $banGhi;
    }
}


