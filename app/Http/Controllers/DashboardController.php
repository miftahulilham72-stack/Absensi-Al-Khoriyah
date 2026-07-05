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
}