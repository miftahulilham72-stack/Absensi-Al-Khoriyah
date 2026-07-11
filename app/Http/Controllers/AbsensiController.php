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
    public function form()
    {
        $sesiAktif = Sesi::where('is_active', true)->first();
        
        if (!$sesiAktif) {
            return view('absensi.form', ['sesiAktif' => null, 'error' => 'Tidak ada sesi aktif saat ini']);
        }

        return view('absensi.form', compact('sesiAktif'));
    }

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

            $ttdPath = $this->saveTtdImage($ttdBase64, $nis);

            $peserta = Peserta::where('nis', $nis)->first();
            if (!$peserta) {
                throw new \Exception('NIS tidak terdaftar!');
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

    public function exportExcel(Request $request)
    {
        $query = Absensi::with(['peserta', 'sesi'])->orderBy('created_at', 'desc');

        if ($request->filled('sesi')) {
            $query->where('sesi_id', $request->sesi);
        }

        $absensi = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['No', 'NIS', 'Nama Lengkap', 'Lembaga', 'Sesi', 'Jam Masuk', 'Status', 'Keterangan', 'Absen Manual', 'Diabsensi Oleh', 'Waktu Absen', 'TTD'];
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
            $sheet->setCellValue('L' . $row, ($data->ttd_image && $data->ttd_image != 'manual_absensi') ? asset($data->ttd_image) : 'Manual');
        }

        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="rekap_absensi_' . date('Y-m-d') . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

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
        
        $sudahAbsenIds = [];
        if ($sesiAktif) {
            $sudahAbsenIds = Absensi::where('sesi_id', $sesiAktif->id)
                                    ->pluck('peserta_id')
                                    ->toArray();
        }
        
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
                $query->whereNotIn('id', $sudahAbsenIds);
            } elseif ($filter == 'sudah' && $sesiAktif) {
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

        return view('absensi.manual', compact('peserta', 'sesiAktif', 'statistik', 'sudahAbsenIds'));
    }

    /**
     * Simpan absensi manual dari panitia - VERSI RAW QUERY (PASTI BERHASIL!)
     */
    public function manualStore(Request $request)
    {
        // ===== AMBIL DATA =====
        $changes = $request->input('changes');
        
        // ===== CEK =====
        if (!is_array($changes) || empty($changes)) {
            return response()->json([
                'success' => false,
                'message' => '❌ Tidak ada data!'
            ]);
        }

        try {
            DB::beginTransaction();

            $sesiAktif = Sesi::where('is_active', true)->first();
            if (!$sesiAktif) {
                throw new \Exception('Tidak ada sesi aktif!');
            }

            $adminName = Auth::user()->name ?? 'Admin';
            $saved = 0;
            $log = [];

            foreach ($changes as $pesertaId => $keterangan) {
                if (!in_array($keterangan, ['Hadir', 'Sakit', 'Izin', 'Alpa'])) {
                    continue;
                }

                // ===== CARI DATA ABSENSI =====
                $absensi = Absensi::where('peserta_id', $pesertaId)
                                  ->where('sesi_id', $sesiAktif->id)
                                  ->first();

                $jamSekarang = now()->format('H:i:s');
                $status = $sesiAktif->getStatus($jamSekarang);

                if ($absensi) {
                    // ===== UPDATE PAKAI RAW QUERY (PASTI!) =====
                    $updated = DB::table('absensi')
                        ->where('id', $absensi->id)
                        ->update([
                            'keterangan' => $keterangan,
                            'absen_manual' => 1,
                            'diabsensi_oleh' => $adminName,
                            'jam_masuk' => $jamSekarang,
                            'status' => $status,
                            'updated_at' => now(),
                        ]);
                    
                    $log[] = "UPDATE ID {$absensi->id} → {$keterangan} (affected: $updated)";
                } else {
                    // ===== CREATE =====
                    $peserta = Peserta::find($pesertaId);
                    if (!$peserta) continue;
                    
                    $newId = DB::table('absensi')->insertGetId([
                        'peserta_id' => $pesertaId,
                        'sesi_id' => $sesiAktif->id,
                        'jam_masuk' => $jamSekarang,
                        'status' => $status,
                        'keterangan' => $keterangan,
                        'ttd_image' => 'manual_absensi',
                        'absen_manual' => 1,
                        'diabsensi_oleh' => $adminName,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    $log[] = "CREATE ID {$newId} → {$keterangan}";
                }
                $saved++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "✅ Berhasil menyimpan {$saved} data!",
                'log' => $log
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '❌ ' . $e->getMessage()
            ]);
        }
    }

    // ================================================================
    // ===== EXPORT WORD (.docx) =====
    // ================================================================

    /**
     * Export data absensi ke Word (.docx) dengan Kop Surat
     */
    public function exportWord(Request $request)
    {
        try {
            // ===== AMBIL DATA =====
            $query = Absensi::with(['peserta', 'sesi'])->orderBy('created_at', 'desc');

            if ($request->filled('sesi')) {
                $query->where('sesi_id', $request->sesi);
            }

            $absensi = $query->get();
            $totalSiswa = $absensi->count();

            // ===== AMBIL JENJANG =====
            $jenjang = $request->get('jenjang', 'mts');
            if (!in_array($jenjang, ['mts', 'ma'])) {
                $jenjang = 'mts';
            }
            $kop = config('kop_surat.' . $jenjang);

            // ===== BUAT PHPWORD =====
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $section = $phpWord->addSection([
                'orientation' => 'portrait',
                'margin' => 720,
            ]);

            // ================================================================
            // KOP SURAT
            // ================================================================
            $fontBoldLarge = ['name' => 'Arial', 'size' => 14, 'bold' => true];
            $fontBoldMed = ['name' => 'Arial', 'size' => 12, 'bold' => true];
            $fontNormal = ['name' => 'Arial', 'size' => 10];
            $fontSmall = ['name' => 'Arial', 'size' => 9];

            // Yayasan
            $section->addText($kop['yayasan'], $fontBoldLarge, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            
            // Nama Madrasah
            $section->addText($kop['nama'], $fontBoldMed, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            
            // Akreditasi
            $section->addText('TERAKREDITASI "' . $kop['akreditasi'] . '"', $fontNormal, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            
            // NSM & NPSN
            $section->addText(
                'NSM : ' . $kop['nsm'] . ' | NPSN : ' . $kop['npsn'],
                $fontSmall,
                ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
            );
            
            // Alamat
            $section->addText($kop['alamat'], $fontSmall, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            $section->addText($kop['kota'], $fontSmall, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            
            // Telepon & Email
            $section->addText(
                'Telp. ' . $kop['telp'] . ' | Email : ' . $kop['email'],
                $fontSmall,
                ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
            );

            // Garis pembatas
            $section->addText(
                '═════════════════════════════════════════════════════════════════',
                ['name' => 'Arial', 'size' => 8],
                ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
            );

            // ===== JUDUL =====
            $section->addText('');
            $section->addText('LAPORAN REKAP ABSENSI', $fontBoldMed, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            $section->addText(
                'Periode: ' . now()->format('d F Y'),
                $fontNormal,
                ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
            );
            $section->addText('');

            // ===== TOTAL =====
            $section->addText('Total Siswa: ' . $totalSiswa . ' orang', $fontNormal);
            $section->addText('');

            // ================================================================
            // TABEL
            // ================================================================
            $table = $section->addTable([
                'borderSize' => 6,
                'borderColor' => '000000',
                'cellMargin' => 60,
            ]);

            // HEADER
            $headers = ['No', 'Kode', 'NIS', 'Nama Lengkap', 'Lembaga', 'Sesi', 'Jam Masuk', 'Status', 'Keterangan'];
            $headerRow = $table->addRow();
            foreach ($headers as $header) {
                $cell = $headerRow->addCell(1800, ['valign' => 'center', 'bgColor' => '1E293B']);
                $cell->addText($header, ['name' => 'Arial', 'size' => 8, 'bold' => true, 'color' => 'FFFFFF'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            }

            // DATA
            foreach ($absensi as $i => $data) {
                $row = $table->addRow();
                $row->addCell(500)->addText(($i + 1), ['name' => 'Arial', 'size' => 8]);
                $row->addCell(1000)->addText($data->sesi->kode_sesi ?? '-', ['name' => 'Arial', 'size' => 8]);
                $row->addCell(1200)->addText($data->peserta->nis ?? '-', ['name' => 'Arial', 'size' => 8]);
                $row->addCell(2500)->addText($data->peserta->nama_lengkap ?? '-', ['name' => 'Arial', 'size' => 8]);
                $row->addCell(1000)->addText($data->peserta->lembaga ?? '-', ['name' => 'Arial', 'size' => 8]);
                $row->addCell(1800)->addText($data->sesi->nama_sesi ?? '-', ['name' => 'Arial', 'size' => 8]);
                $row->addCell(1000)->addText($data->jam_masuk ?? '-', ['name' => 'Arial', 'size' => 8]);
                $row->addCell(1500)->addText($data->status ?? '-', ['name' => 'Arial', 'size' => 8]);
                $row->addCell(1500)->addText($data->keterangan ?? '-', ['name' => 'Arial', 'size' => 8]);
            }

            // ===== TANDA TANGAN =====
            $section->addText('');
            $section->addText('');
            $section->addText('');

            $ttdTable = $section->addTable(['borderSize' => 0]);
            
            $ttdRow1 = $ttdTable->addRow();
            $ttdRow1->addCell(5000, ['borderSize' => 0])->addText('Mengetahui,', ['name' => 'Arial', 'size' => 10]);
            $ttdRow1->addCell(5000, ['borderSize' => 0])->addText('Ciamis, ' . now()->format('d F Y'), ['name' => 'Arial', 'size' => 10]);

            $ttdRow2 = $ttdTable->addRow();
            $ttdRow2->addCell(5000, ['borderSize' => 0])->addText('Kepala Madrasah', ['name' => 'Arial', 'size' => 10]);
            $ttdRow2->addCell(5000, ['borderSize' => 0])->addText('Petugas Absensi', ['name' => 'Arial', 'size' => 10]);

            $ttdRow3 = $ttdTable->addRow();
            $ttdRow3->addCell(5000, ['borderSize' => 0])->addText('');
            $ttdRow3->addCell(5000, ['borderSize' => 0])->addText('');

            $ttdRow4 = $ttdTable->addRow();
            $ttdRow4->addCell(5000, ['borderSize' => 0])->addText('_________________________', ['name' => 'Arial', 'size' => 10]);
            $ttdRow4->addCell(5000, ['borderSize' => 0])->addText('_________________________', ['name' => 'Arial', 'size' => 10]);

            // ===== FOOTER =====
            $section->addText('');
            $section->addText(
                'Dicetak dari Sistem Absensi Al-Khoeriyah | ' . now()->format('d/m/Y H:i:s'),
                ['name' => 'Arial', 'size' => 8, 'italic' => true],
                ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
            );

            // ===== SAVE & DOWNLOAD =====
            $filename = 'rekap_absensi_' . date('Y-m-d') . '.docx';
            $tempFile = storage_path('app/public/' . $filename);
            
            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($tempFile);
            
            return response()->download($tempFile)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            \Log::error('Export Word Error: ' . $e->getMessage());
            return back()->with('error', '❌ Gagal export Word: ' . $e->getMessage());
        }
    }
}