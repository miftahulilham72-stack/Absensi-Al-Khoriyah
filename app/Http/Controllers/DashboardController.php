<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Peserta;
use App\Models\Sesi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard
     */
    public function index()
    {
        // Total peserta
        $totalPeserta = Peserta::count();
        
        // Sesi aktif
        $sesiAktif = Sesi::where('is_active', true)->first();
        
        // Statistik hari ini
        $today = now()->toDateString();

        // ===== STATISTIK DARI KOLOM "KETERANGAN" =====
        $hadir = Absensi::whereDate('created_at', $today)
                        ->where('keterangan', 'Hadir')
                        ->count();
        
        $sakit = Absensi::whereDate('created_at', $today)
                        ->where('keterangan', 'Sakit')
                        ->count();
        
        $izin = Absensi::whereDate('created_at', $today)
                       ->where('keterangan', 'Izin')
                       ->count();
        
        $alpa = Absensi::whereDate('created_at', $today)
                       ->where('keterangan', 'Alpa')
                       ->count();

        // Total yang sudah absen
        $sudahAbsen = $hadir + $sakit + $izin + $alpa;
        $belumHadir = $totalPeserta - $sudahAbsen;

        // ===== TERLAMBAT DARI KOLOM "STATUS" =====
        $terlambat = Absensi::whereDate('created_at', $today)
                            ->where(function($q) {
                                $q->where('status', 'Terlambat (Toleransi)')
                                  ->orWhere('status', 'Terlambat');
                            })
                            ->count();

        // Log terbaru
        $logs = Absensi::with(['peserta', 'sesi'])
                       ->orderBy('created_at', 'desc')
                       ->limit(10)
                       ->get();
        
        // Statistik per lembaga
        $statistikLembaga = Peserta::select('lembaga', DB::raw('count(*) as total'))
                                   ->groupBy('lembaga')
                                   ->get();

        return view('dashboard.index', compact(
            'totalPeserta',
            'sesiAktif',
            'hadir',
            'sakit',
            'izin',
            'alpa',
            'sudahAbsen',
            'belumHadir',
            'terlambat',
            'logs',
            'statistikLembaga'
        ));
    }

    /**
     * Tampilkan halaman reset data
     */
    public function resetData()
    {
        return view('dashboard.reset');
    }

    /**
     * Proses reset data (hapus semua data peserta & absensi)
     */
    public function resetDataProcess(Request $request)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        $validPassword = 'reset123'; // Ganti sesuai keinginan

        if ($request->password !== $validPassword) {
            return back()->with('error', '❌ Password salah! Data tidak dihapus.');
        }

        try {
            DB::beginTransaction();

            $absensiDeleted = Absensi::count();
            Absensi::truncate();

            $pesertaDeleted = Peserta::count();
            Peserta::truncate();

            DB::statement('ALTER TABLE absensi AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE peserta AUTO_INCREMENT = 1');

            DB::commit();

            return redirect('/dashboard')->with('success', 
                "✅ Berhasil menghapus {$pesertaDeleted} data peserta dan {$absensiDeleted} data absensi!"
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ Gagal reset data: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan halaman reset password admin
     */
    public function resetPassword()
    {
        return view('dashboard.reset-password');
    }

    /**
     * Proses reset password admin
     */
    public function resetPasswordProcess(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:4|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->with('error', '❌ Password lama salah!');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', '✅ Password berhasil diubah!');
    }
}