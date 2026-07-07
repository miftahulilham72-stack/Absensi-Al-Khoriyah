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
     * Proses absensi digital siswa (dengan TTD) - TANPA STORED PROCEDURE
     * Tetap menggunakan ACID (Database Transaction)
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
            // ===== ACID: BEGIN TRANSACTION =====
            DB::beginTransaction();

            $nis = $request->nis;
            $ttdBase64 = $request->ttd;

            // 1. Cek peserta
            $peserta = Peserta::where('nis', $nis)->first();
            if (!$peserta) {
                throw new \Exception('NIS tidak terdaftar! Silakan hubungi panitia.');
            }

            // 2. Cek sesi aktif
            $sesi = Sesi::where('is_active', true)->first();
            if (!$sesi) {
                throw new \Exception('Tidak ada sesi aktif saat ini!');
            }

            // 3. Cek duplikat absensi
            $sudahAbsen = Absensi::where('peserta_id', $peserta->id)
                                ->where('sesi_id', $sesi->id)
                                ->exists();
            if ($sudahAbsen) {
                throw new \Exception($peserta->nama_lengkap . ' sudah melakukan absen untuk sesi ini!');
            }

            // 4. Cek TTD tidak boleh kosong
            if (empty($ttdBase64) || strlen($ttdBase64) < 100) {
                throw new \Exception('Tanda tangan wajib diisi!');
            }

            // 5. Simpan gambar TTD
            $ttdPath = $this->saveTtdImage($ttdBase64, $nis);

            // 6. Tentukan status
            $jamSekarang = now()->format('H:i:s');
            $status = $sesi->getStatus($jamSekarang);

            // 7. Simpan absensi
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

            // ===== ACID: COMMIT TRANSACTION =====
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Halo ' . $peserta->nama_lengkap . '! Absen berhasil. Status: ' . $status
            ]);

        } catch (\Exception $e) {
            // ===== ACID: ROLLBACK TRANSACTION =====
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

        $pdf = Pdf::loadView('absensi.pdf', compact('absensi'));
        return $pdf->download('rekap_absensi_' . date('Y-m-d') . '.pdf');
    }

    // ================================================================
    // ===== KIOSK MODE =====
    // ================================================================

    public function kiosk(Request $request)
    {
        $lembaga = $request->get('lembaga', 'semua');
        
        if (!in_array($lembaga, ['MTs', 'MA', 'semua'])) {
            $lembaga = 'semua';
        }

        $sesiAktif = Sesi::where('is_active', true)->first();

        return view('absensi.kiosk', compact('lembaga', 'sesiAktif'));
    }

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
            $status = $sesi->getStatus($jamSekarang);

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

    // ================================================================
    // ===== ABSENSI MANUAL =====
    // ================================================================

    public function manual(Request $request)
    {
        $sesiAktif = Sesi::where('is_active', true)->first();
        
        $query = Peserta::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nis', 'like', "%$search%")
                  ->orWhere('nama_lengkap', 'like', "%$search%");
            });
        }

        if ($request->filled('lembaga')) {
            $query->where('lembaga', $request->lembaga);
        }

        if ($request->filled('status_filter')) {
            $filter = $request->status_filter;
            if ($filter == 'belum' && $sesiAktif) {
                $sudahAbsenIds = Absensi::where('sesi_id', $sesiAktif->id)
                                        ->pluck('peserta_id')
                                        ->toArray();
                $query->whereNotIn('id', $sudahAbsenIds);
            } elseif ($filter == 'sudah' && $sesiAktif) {
                $sudahAbsenIds = Absensi::where('sesi_id', $sesiAktif->id)
                                        ->pluck('peserta_id')
                                        ->toArray();
                $query->whereIn('id', $sudahAbsenIds);
            }
        }

        if ($sesiAktif) {
            $query->with(['absensi_manual' => function($q) use ($sesiAktif) {
                $q->where('sesi_id', $sesiAktif->id);
            }]);
        }

        $peserta = $query->orderBy('nama_lengkap')->paginate(20);

        $statistik = ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpa' => 0];
        if ($sesiAktif) {
            $statistik['hadir'] = Absensi::where('sesi_id', $sesiAktif->id)->where('keterangan', 'Hadir')->count();
            $statistik['sakit'] = Absensi::where('sesi_id', $sesiAktif->id)->where('keterangan', 'Sakit')->count();
            $statistik['izin'] = Absensi::where('sesi_id', $sesiAktif->id)->where('keterangan', 'Izin')->count();
            $statistik['alpa'] = Absensi::where('sesi_id', $sesiAktif->id)->where('keterangan', 'Alpa')->count();
        }

        return view('absensi.manual', compact('peserta', 'sesiAktif', 'statistik'));
    }

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
                $existing = Absensi::where('peserta_id', $pesertaId)
                                   ->where('sesi_id', $sesiAktif->id)
                                   ->first();

                $jamSekarang = now()->format('H:i:s');
                $status = $sesiAktif->getStatus($jamSekarang);

                if ($existing) {
                    $existing->update([
                        'keterangan' => $keterangan,
                        'absen_manual' => true,
                        'diabsensi_oleh' => $adminName,
                        'jam_masuk' => $jamSekarang,
                        'status' => $status,
                    ]);
                } else {
                    $peserta = Peserta::find($pesertaId);
                    if (!$peserta) continue;
                    
                    Absensi::create([
                        'peserta_id' => $pesertaId,
                        'sesi_id' => $sesiAktif->id,
                        'jam_masuk' => $jamSekarang,
                        'status' => $status,
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
}