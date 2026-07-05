<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Peserta;
use App\Models\Sesi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function count()
    {
        $totalPeserta = Peserta::count();
        $sudahAbsen = Absensi::whereDate('created_at', Carbon::today())->count();
        $belumAbsen = $totalPeserta - $sudahAbsen;

        $notifications = [];

        if ($belumAbsen > 0) {
            $notifications[] = [
                'id' => 1,
                'title' => '⚠️ Siswa Belum Absen',
                'message' => "Terdapat {$belumAbsen} siswa yang belum melakukan absen hari ini",
                'time' => Carbon::now()->format('H:i')
            ];
        }

        $sesiAktif = Sesi::where('is_active', true)->first();
        if (!$sesiAktif) {
            $notifications[] = [
                'id' => 2,
                'title' => '⚠️ Tidak Ada Sesi Aktif',
                'message' => 'Silakan aktifkan sesi untuk memulai absensi',
                'time' => Carbon::now()->format('H:i')
            ];
        }

        $terlambat = Absensi::whereDate('created_at', Carbon::today())
            ->where('status', 'Terlambat')
            ->count();

        if ($terlambat > 0) {
            $notifications[] = [
                'id' => 3,
                'title' => '⏰ Siswa Terlambat',
                'message' => "{$terlambat} siswa datang terlambat hari ini",
                'time' => Carbon::now()->format('H:i')
            ];
        }

        return response()->json([
            'count' => count($notifications),
            'notifications' => $notifications,
        ]);
    }

    public function markRead($id)
    {
        session()->put('notif_read_' . $id, true);

        return response()->json(['success' => true]);
    }
}
