<?php

namespace App\Providers;

use App\Models\Absensi;
use App\Models\Peserta;
use App\Models\Sesi;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('partials.sidebar', function ($view) {
            $sesiAktif = Sesi::where('is_active', true)->first();
            $showManual = false;
            $belumAbsen = 0;
            
            if ($sesiAktif) {
                $totalPeserta = Peserta::count();
                $sudahAbsen = Absensi::where('sesi_id', $sesiAktif->id)->count();
                $belumAbsen = $totalPeserta - $sudahAbsen;
                $showManual = $belumAbsen > 0;
            }
            
            $view->with([
                'showManual' => $showManual,
                'belumAbsen' => $belumAbsen,
                'sesiAktif' => $sesiAktif,
            ]);
        });
    }

    public function register(): void
    {
        //
    }
}