<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Peserta;
use App\Models\Sesi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class AbsensiController extends Controller
{
    /**
     * Tampilkan form absensi untuk siswa (digital)
     */
    public function form()
    {
        $sesiAktif = Sesi::where('is_active', true)->first();
        
        if (!$sesiAktif) {
            return view('absensi.form', ['sesiAktif' => null, 'error' => 'Tidak ada sesi aktif saat ini']);
        }

        return view('absensi.form', compact('sesiAktif'));
    }

    /**
     * Proses absensi digital siswa (dengan TTD)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nis' => 'required|string|exists:peserta,nis',
            'ttd' => 'required|string|min:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $nis = $request->nis;
            $ttdBase64 = $request->ttd;

            // Simpan TTD sebagai file
            $ttdPath = $this->saveTtdImage($ttdBase64, $nis);

            // Cari peserta
            $peserta = Peserta::where('nis', $nis)->first();
            if (!$peserta) {
                throw new \Exception('NIS tidak terdaftar!');
            }

            // Cari sesi aktif
            $sesi = Sesi::where('is_active', true)->first();
            if (!$sesi) {
                throw new \Exception('Tidak ada sesi aktif!');
            }

            // Cek duplikat
            $sudahAbsen = Absensi::where('peserta_id', $peserta->id)
                                ->where('sesi_id', $sesi->id)
                                ->exists();
            if ($sudahAbsen) {
                throw new \Exception($peserta->nama_lengkap . ' sudah absen!');
            }

            // Tentukan status
            $jamSekarang = now()->format('H:i:s');
            $status = $jamSekarang <= $sesi->batas_waktu ? 'Tepat Waktu' : 'Terlambat';

            // Simpan absensi
            Absensi::create([
                'peserta_id' => $peserta->id,
                'sesi_id' => $sesi->id,
                'jam_masuk' => $jamSekarang,
                'status' => $status,
                'keterangan' => 'Hadir',
                'ttd_image' => $ttdPath,
                'absen_manual' => false,
                'diabsensi_oleh' => null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Halo ' . $peserta->nama_lengkap . '! Absen berhasil. Status: ' . $status
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Simpan gambar TTD
     */
    private function saveTtdImage($base64, $nis)
    {
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
        $filename = 'ttd_' . $nis . '_' . time() . '.png';
        $path = public_path('uploads/ttd/' . $filename);
        
        if (!file_exists(public_path('uploads/ttd'))) {
            mkdir(public_path('uploads/ttd'), 0755, true);
        }
        
        file_put_contents($path, $imageData);
        return 'uploads/ttd/' . $filename;
    }

    /**
     * Tampilkan log riwayat absensi
     */
    public function log(Request $request)
    {
        $query = Absensi::with(['peserta', 'sesi'])->orderBy('created_at', 'desc');

        if ($request->filled('sesi')) {
            $query->where('sesi_id', $request->sesi);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('peserta', function($q) use ($search) {
                $q->where('nis', 'like', "%$search%")
                  ->orWhere('nama_lengkap', 'like', "%$search%");
            });
        }

        $absensi = $query->paginate(20);
        $sesiList = Sesi::all();

        return view('absensi.log', compact('absensi', 'sesiList'));
    }

    /**
     * Export data absensi ke Excel
     */
    public function exportExcel(Request $request)
    {
        $query = Absensi::with(['peserta', 'sesi'])->orderBy('created_at', 'desc');

        if ($request->filled('sesi')) {
            $query->where('sesi_id', $request->sesi);
        }

        $absensi = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = ['No', 'NIS', 'Nama Lengkap', 'Lembaga', 'Sesi', 'Jam Masuk', 'Status', 'Keterangan', 'Absen Manual', 'Diabsensi Oleh', 'Waktu Absen'];
        foreach ($headers as $i => $header) {
            $col = chr(65 + $i);
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getFill()
                  ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                  ->getStartColor()->setARGB('FF1E3A8A');
            $sheet->getStyle($col . '1')->getFont()->getColor()->setARGB('FFFFFFFF');
        }

        // Data
        foreach ($absensi as $i => $data) {
            $row = $i + 2;
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $data->peserta->nis ?? '-');
            $sheet->setCellValue('C' . $row, $data->peserta->nama_lengkap ?? '-');
            $sheet->setCellValue('D' . $row, $data->peserta->lembaga ?? '-');
            $sheet->setCellValue('E' . $row, $data->sesi->nama_sesi ?? '-');
            $sheet->setCellValue('F' . $row, $data->jam_masuk ?? '-');
            $sheet->setCellValue('G' . $row, $data->status ?? '-');
            $sheet->setCellValue('H' . $row, $data->keterangan ?? '-');
            $sheet->setCellValue('I' . $row, $data->absen_manual ? 'Ya' : 'Tidak');
            $sheet->setCellValue('J' . $row, $data->diabsensi_oleh ?? '-');
            $sheet->setCellValue('K' . $row, $data->created_at ? $data->created_at->format('H:i:s d/m/Y') : '-');
        }

        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="rekap_absensi_' . date('Y-m-d') . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    /**
     * Export data absensi ke PDF
     */
    public function exportPdf(Request $request)
    {
        $query = Absensi::with(['peserta', 'sesi'])->orderBy('created_at', 'desc');

        if ($request->filled('sesi')) {
            $query->where('sesi_id', $request->sesi);
        }

        $absensi = $query->get();
        $sesiList = Sesi::all();

        $pdf = Pdf::loadView('absensi.pdf', compact('absensi', 'sesiList'));
        return $pdf->download('rekap_absensi_' . date('Y-m-d') . '.pdf');
    }

    // ================================================================
    // ===== ABSENSI MANUAL - FITUR UNTUK PANITIA/ADMIN =====
    // ================================================================

    /**
     * Tampilkan halaman absensi manual untuk panitia
     */
    public function manual(Request $request)
    {
        $sesiAktif = Sesi::where('is_active', true)->first();
        
        // Ambil semua peserta dengan filter
        $query = Peserta::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nis', 'like', "%$search%")
                  ->orWhere('nama_lengkap', 'like', "%$search%");
            });
        }

        // ===== FILTER LEMBAGA - BARU! =====
        if ($request->filled('lembaga')) {
            $query->where('lembaga', $request->lembaga);
        }

        if ($request->filled('status_filter')) {
            $filter = $request->status_filter;
            if ($filter == 'belum') {
                // Hanya tampilkan yang belum absen di sesi aktif
                if ($sesiAktif) {
                    $sudahAbsenIds = Absensi::where('sesi_id', $sesiAktif->id)
                                            ->pluck('peserta_id')
                                            ->toArray();
                    $query->whereNotIn('id', $sudahAbsenIds);
                }
            } elseif ($filter == 'sudah') {
                // Hanya tampilkan yang sudah absen di sesi aktif
                if ($sesiAktif) {
                    $sudahAbsenIds = Absensi::where('sesi_id', $sesiAktif->id)
                                            ->pluck('peserta_id')
                                            ->toArray();
                    $query->whereIn('id', $sudahAbsenIds);
                }
            }
        }

        // Eager load absensi manual untuk sesi aktif
        if ($sesiAktif) {
            $query->with(['absensi_manual' => function($q) use ($sesiAktif) {
                $q->where('sesi_id', $sesiAktif->id);
            }]);
        }

        $peserta = $query->orderBy('nama_lengkap')->paginate(20);

        // Statistik
        $statistik = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpa' => 0
        ];
        
        if ($sesiAktif) {
            $statistik['hadir'] = Absensi::where('sesi_id', $sesiAktif->id)
                                        ->where('keterangan', 'Hadir')
                                        ->count();
            $statistik['sakit'] = Absensi::where('sesi_id', $sesiAktif->id)
                                        ->where('keterangan', 'Sakit')
                                        ->count();
            $statistik['izin'] = Absensi::where('sesi_id', $sesiAktif->id)
                                        ->where('keterangan', 'Izin')
                                        ->count();
            $statistik['alpa'] = Absensi::where('sesi_id', $sesiAktif->id)
                                        ->where('keterangan', 'Alpa')
                                        ->count();
        }

        return view('absensi.manual', compact('peserta', 'sesiAktif', 'statistik'));
    }

    /**
     * Simpan absensi manual dari panitia
     */
    public function manualStore(Request $request)
    {
        $request->validate([
            'changes' => 'required|array'
        ]);

        try {
            DB::beginTransaction();

            $sesiAktif = Sesi::where('is_active', true)->first();
            if (!$sesiAktif) {
                throw new \Exception('Tidak ada sesi aktif!');
            }

            $adminName = Auth::user()->name ?? 'Admin';
            $saved = 0;

            foreach ($request->changes as $pesertaId => $keterangan) {
                // Cek apakah peserta sudah absen di sesi ini
                $existing = Absensi::where('peserta_id', $pesertaId)
                                   ->where('sesi_id', $sesiAktif->id)
                                   ->first();

                if ($existing) {
                    // Update jika sudah ada
                    $existing->update([
                        'keterangan' => $keterangan,
                        'absen_manual' => true,
                        'diabsensi_oleh' => $adminName,
                        'jam_masuk' => now()->format('H:i:s'),
                    ]);
                } else {
                    // Buat baru
                    $peserta = Peserta::find($pesertaId);
                    if (!$peserta) {
                        continue;
                    }
                    
                    Absensi::create([
                        'peserta_id' => $pesertaId,
                        'sesi_id' => $sesiAktif->id,
                        'jam_masuk' => now()->format('H:i:s'),
                        'status' => $keterangan == 'Hadir' ? 'Tepat Waktu' : 'Terlambat',
                        'keterangan' => $keterangan,
                        'ttd_image' => 'manual_absensi',
                        'absen_manual' => true,
                        'diabsensi_oleh' => $adminName,
                    ]);
                }
                $saved++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil menyimpan $saved data absensi manual!"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Tampilkan halaman kiosk untuk peserta
     */
    public function kiosk(Request $request)
    {
        $lembaga = $request->get('lembaga', 'semua');

        if (!in_array($lembaga, ['MTs', 'MA', 'semua'])) {
            $lembaga = 'semua';
        }

        $sesiAktif = Sesi::where('is_active', true)->first();

        return view('absensi.kiosk', compact('lembaga', 'sesiAktif'));
    }

    /**
     * Proses absensi dari mode kiosk
     */
    public function kioskStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nis' => 'required|string|exists:peserta,nis',
            'ttd' => 'required|string|min:100',
            'lembaga' => 'required|string|in:MTs,MA,semua'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $nis = $request->nis;
            $ttdBase64 = $request->ttd;
            $lembagaFilter = $request->lembaga;

            $peserta = Peserta::where('nis', $nis)->first();
            if (!$peserta) {
                throw new \Exception('NIS tidak terdaftar!');
            }

            if ($lembagaFilter !== 'semua' && $peserta->lembaga !== $lembagaFilter) {
                throw new \Exception('NIS terdaftar di ' . $peserta->lembaga . ', bukan ' . $lembagaFilter . '!');
            }

            $sesi = Sesi::where('is_active', true)->first();
            if (!$sesi) {
                throw new \Exception('Tidak ada sesi aktif!');
            }

            $sudahAbsen = Absensi::where('peserta_id', $peserta->id)
                                ->where('sesi_id', $sesi->id)
                                ->exists();
            if ($sudahAbsen) {
                throw new \Exception($peserta->nama_lengkap . ' sudah absen!');
            }

            $ttdPath = $this->saveTtdImage($ttdBase64, $nis);

            $jamSekarang = now()->format('H:i:s');
            $status = $jamSekarang <= $sesi->batas_waktu ? 'Tepat Waktu' : 'Terlambat';

            Absensi::create([
                'peserta_id' => $peserta->id,
                'sesi_id' => $sesi->id,
                'jam_masuk' => $jamSekarang,
                'status' => $status,
                'keterangan' => 'Hadir',
                'ttd_image' => $ttdPath,
                'absen_manual' => false,
                'diabsensi_oleh' => null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Halo ' . $peserta->nama_lengkap . '! Absen berhasil. Status: ' . $status,
                'data' => [
                    'nama' => $peserta->nama_lengkap,
                    'status' => $status
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get counter absensi real-time
     */
    public function counter(Request $request)
    {
        $today = now()->toDateString();

        $mts = Absensi::whereDate('created_at', $today)
                      ->whereHas('peserta', function($q) {
                          $q->where('lembaga', 'MTs');
                      })->count();

        $ma = Absensi::whereDate('created_at', $today)
                     ->whereHas('peserta', function($q) {
                         $q->where('lembaga', 'MA');
                     })->count();

        $total = $mts + $ma;
        $totalSiswa = Peserta::count();

        return response()->json([
            'mts' => $mts,
            'ma' => $ma,
            'total' => $total,
            'total_siswa' => $totalSiswa
        ]);
    }
}