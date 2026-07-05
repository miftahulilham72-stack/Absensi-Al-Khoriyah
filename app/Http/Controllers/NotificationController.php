<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Peserta;
use App\Models\Sesi;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function count()
    {
        // Ambil notifikasi yang sudah dibaca dari session
        $readNotifications = session('read_notifications', []);
        
        $totalPeserta = Peserta::count();
        $sudahAbsen = Absensi::whereDate('created_at', Carbon::today())->count();
        $belumAbsen = $totalPeserta - $sudahAbsen;

        $notifications = [];
        $notificationIds = [];

        if ($belumAbsen > 0) {
            $id = 'belum_absen';
            $notificationIds[] = $id;
            if (!in_array($id, $readNotifications)) {
                $notifications[] = [
                    'id' => $id,
                    'title' => '⚠️ Siswa Belum Absen',
                    'message' => "Terdapat {$belumAbsen} siswa yang belum melakukan absen hari ini",
                    'time' => Carbon::now()->format('H:i'),
                    'type' => 'warning'
                ];
            }
        }

        $sesiAktif = Sesi::where('is_active', true)->first();
        if (!$sesiAktif) {
            $id = 'no_active_session';
            $notificationIds[] = $id;
            if (!in_array($id, $readNotifications)) {
                $notifications[] = [
                    'id' => $id,
                    'title' => '⚠️ Tidak Ada Sesi Aktif',
                    'message' => 'Silakan aktifkan sesi untuk memulai absensi',
                    'time' => Carbon::now()->format('H:i'),
                    'type' => 'danger'
                ];
            }
        }

        $terlambat = Absensi::whereDate('created_at', Carbon::today())
                            ->where('status', 'Terlambat')
                            ->count();
        if ($terlambat > 0) {
            $id = 'siswa_terlambat';
            $notificationIds[] = $id;
            if (!in_array($id, $readNotifications)) {
                $notifications[] = [
                    'id' => $id,
                    'title' => '⏰ Siswa Terlambat',
                    'message' => "{$terlambat} siswa datang terlambat hari ini",
                    'time' => Carbon::now()->format('H:i'),
                    'type' => 'danger'
                ];
            }
        }

        return response()->json([
            'count' => count($notifications),
            'notifications' => $notifications,
            'all_read' => count($notifications) === 0
        ]);
    }

    public function markRead($id)
    {
        $readNotifications = session('read_notifications', []);
        if (!in_array($id, $readNotifications)) {
            $readNotifications[] = $id;
            session(['read_notifications' => $readNotifications]);
        }
        return response()->json(['success' => true]);
    }

    public function markAllRead()
    {
        // Tandai semua notifikasi telah dibaca
        $allIds = [];
        
        // Kumpulkan semua ID notifikasi yang mungkin
        $totalPeserta = Peserta::count();
        $sudahAbsen = Absensi::whereDate('created_at', Carbon::today())->count();
        $belumAbsen = $totalPeserta - $sudahAbsen;
        
        if ($belumAbsen > 0) {
            $allIds[] = 'belum_absen';
        }
        
        $sesiAktif = Sesi::where('is_active', true)->first();
        if (!$sesiAktif) {
            $allIds[] = 'no_active_session';
        }
        
        $terlambat = Absensi::whereDate('created_at', Carbon::today())
                            ->where('status', 'Terlambat')
                            ->count();
        if ($terlambat > 0) {
            $allIds[] = 'siswa_terlambat';
        }
        
        session(['read_notifications' => $allIds]);
        
        return response()->json(['success' => true]);
    }

    public function resetNotifications()
    {
        // Reset semua notifikasi (untuk testing)
        session()->forget('read_notifications');
        return response()->json(['success' => true]);
    }
}