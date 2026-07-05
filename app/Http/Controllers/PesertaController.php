<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class PesertaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $peserta = Peserta::orderBy('nama_lengkap')->paginate(15);
        $total = Peserta::count();
        return view('peserta.index', compact('peserta', 'total'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('peserta.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nis' => 'required|string|unique:peserta,nis|max:20',
            'nama_lengkap' => 'required|string|max:100',
            'lembaga' => 'required|in:MTs,MA',
            'gugus' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $peserta = Peserta::create($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Siswa berhasil ditambahkan!',
                'data' => $peserta
            ]);
        }

        return redirect()->route('peserta.index')->with('success', 'Siswa berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $peserta = Peserta::findOrFail($id);
        
        if (request()->ajax()) {
            return response()->json($peserta);
        }
        
        return view('peserta.show', compact('peserta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $peserta = Peserta::findOrFail($id);
        return view('peserta.edit', compact('peserta'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $peserta = Peserta::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nis' => 'required|string|max:20|unique:peserta,nis,' . $id,
            'nama_lengkap' => 'required|string|max:100',
            'lembaga' => 'required|in:MTs,MA',
            'gugus' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $peserta->update($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data siswa berhasil diupdate!',
                'data' => $peserta
            ]);
        }

        return redirect()->route('peserta.index')->with('success', 'Data siswa berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $peserta = Peserta::findOrFail($id);
        $peserta->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Siswa berhasil dihapus!'
            ]);
        }

        return redirect()->route('peserta.index')->with('success', 'Siswa berhasil dihapus!');
    }

    // ============================================================
    // ===== CUSTOM METHODS =====
    // ============================================================

    /**
     * Cari peserta berdasarkan NIS (untuk auto-fill di form absensi)
     */
    public function cari($nis)
    {
        $peserta = Peserta::where('nis', $nis)->first();
        
        if ($peserta) {
            return response()->json([
                'found' => true,
                'nama' => $peserta->nama_lengkap,
                'data' => $peserta
            ]);
        }

        return response()->json([
            'found' => false,
            'message' => 'NIS tidak ditemukan'
        ]);
    }

    /**
     * Tampilkan form import Excel
     */
    public function importForm()
    {
        return view('peserta.import');
    }

    /**
     * Proses import data dari file Excel
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            array_shift($rows);

            $success = 0;
            $errors = [];
            $duplicates = [];

            foreach ($rows as $row) {
                $nis = trim($row[0] ?? '');
                $nama = trim($row[1] ?? '');
                $lembaga = trim($row[2] ?? '');
                $gugus = trim($row[3] ?? '');

                if (empty($nis) && empty($nama) && empty($lembaga) && empty($gugus)) {
                    continue;
                }

                if (empty($nis) || empty($nama)) {
                    $errors[] = "Data tidak lengkap: NIS '$nis', Nama '$nama'";
                    continue;
                }

                if (!in_array($lembaga, ['MTs', 'MA'], true)) {
                    $errors[] = "Lembaga '$lembaga' tidak valid untuk NIS $nis (harus MTs atau MA)";
                    continue;
                }

                try {
                    $existing = Peserta::where('nis', $nis)->first();
                    if ($existing) {
                        $duplicates[] = "NIS $nis ($nama) sudah terdaftar, dilewati.";
                        continue;
                    }

                    Peserta::create([
                        'nis' => $nis,
                        'nama_lengkap' => $nama,
                        'lembaga' => $lembaga,
                        'gugus' => $gugus,
                    ]);
                    $success++;
                } catch (\Exception $e) {
                    $errors[] = "Gagal import NIS $nis: " . $e->getMessage();
                }
            }

            $message = "✅ Berhasil import $success data!";
            if (count($duplicates) > 0) {
                $message .= " ⚠️ " . count($duplicates) . " data duplikat dilewati.";
            }

            return redirect()->route('peserta.import.form')
                ->with('success', $message)
                ->with('errors', $errors)
                ->with('duplicates', $duplicates);
        } catch (\Exception $e) {
            return redirect()->route('peserta.import.form')
                ->with('error', '❌ Gagal membaca file: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel untuk import data
     */
    public function exportTemplate()
    {
        try {
            // Buat spreadsheet baru
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // ===== HEADER =====
            $sheet->setCellValue('A1', 'NIS');
            $sheet->setCellValue('B1', 'Nama Lengkap');
            $sheet->setCellValue('C1', 'Lembaga (MTs/MA)');
            $sheet->setCellValue('D1', 'Gugus/Kelompok');

            // Style header: Bold, Background Biru, Text Putih
            $headerStyle = $sheet->getStyle('A1:D1');
            $headerStyle->getFont()->setBold(true)->setSize(12);
            $headerStyle->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF1E3A8A');
            $headerStyle->getFont()->getColor()->setARGB('FFFFFFFF');

            // ===== CONTOH DATA =====
            $sheet->setCellValue('A2', '2510614001');
            $sheet->setCellValue('B2', 'Ahmad Fauzi Rahman');
            $sheet->setCellValue('C2', 'MA');
            $sheet->setCellValue('D2', 'Kelompok Al-Fatih');

            $sheet->setCellValue('A3', '2510614002');
            $sheet->setCellValue('B3', 'Siti Aminah Zahra');
            $sheet->setCellValue('C3', 'MTs');
            $sheet->setCellValue('D3', 'Kelompok Salahuddin');

            $sheet->setCellValue('A4', '2510614003');
            $sheet->setCellValue('B4', 'Budi Setiawan');
            $sheet->setCellValue('C4', 'MA');
            $sheet->setCellValue('D4', 'Kelompok Khalid bin Walid');

            // ===== AUTO SIZE COLUMNS =====
            foreach (range('A', 'D') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // ===== BORDER =====
            $styleArray = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCCCCCC'],
                    ],
                ],
            ];
            $sheet->getStyle('A1:D4')->applyFromArray($styleArray);

            // ===== SAVE FILE =====
            $writer = new Xlsx($spreadsheet);
            
            // Set header untuk download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="template_import_peserta.xlsx"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public');
            
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            return redirect()->route('peserta.import.form')
                ->with('error', '❌ Gagal generate template: ' . $e->getMessage());
        }
    }
}