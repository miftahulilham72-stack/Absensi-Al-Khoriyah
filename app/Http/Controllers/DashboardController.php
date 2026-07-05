<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Peserta;
use App\Models\Sesi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total peserta
        $totalPeserta = Peserta::count();
        
        // Sesi aktif
        $sesiAktif = Sesi::where('is_active', true)->first();
        
        // Statistik hari ini
        $today = now()->toDateString();
        $hadir = Absensi::whereDate('created_at', $today)->count();
        $belumHadir = $totalPeserta - $hadir;
        
        // Terlambat hari ini
        $terlambat = Absensi::whereDate('created_at', $today)
                            ->where('status', 'Terlambat')
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
     * Proses reset data
     */
    public function resetDataProcess(Request $request)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        // Ganti dengan password yang Anda inginkan!
        $validPassword = 'reset123'; // Bisa diganti

        if ($request->password !== $validPassword) {
            return back()->with('error', '❌ Password salah! Data tidak dihapus.');
        }

        try {
            DB::beginTransaction();

            // Hapus semua data absensi
            $absensiDeleted = Absensi::count();
            Absensi::truncate();

            // Hapus semua data peserta
            $pesertaDeleted = Peserta::count();
            Peserta::truncate();

            // Reset auto-increment
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
}