<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class PesertaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Peserta::query();
        
        // Search by NIS or Nama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nis', 'like', "%$search%")
                  ->orWhere('nama_lengkap', 'like', "%$search%");
            });
        }
        
        // Filter Lembaga
        if ($request->filled('lembaga')) {
            $query->where('lembaga', $request->lembaga);
        }
        
        $peserta = $query->orderBy('nama_lengkap')->paginate(15);
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
                'message' => '✅ Siswa berhasil ditambahkan!',
                'data' => $peserta
            ]);
        }

        return redirect()->route('peserta.index')->with('success', '✅ Siswa berhasil ditambahkan!');
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
                'message' => '✅ Data siswa berhasil diupdate!',
                'data' => $peserta
            ]);
        }

        return redirect()->route('peserta.index')->with('success', '✅ Data siswa berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     * 
     * MENGGUNAKAN REDIRECT (BUKAN JSON) UNTUK MENGHINDARI ERROR 404/405
     */
    public function destroy($id)
    {
        try {
            $peserta = Peserta::findOrFail($id);
            
            // Hapus data absensi terkait
            Absensi::where('peserta_id', $id)->delete();
            
            $peserta->delete();

            return redirect()->route('peserta.index')->with('success', '✅ Siswa berhasil dihapus!');
            
        } catch (\Exception $e) {
            return redirect()->route('peserta.index')->with('error', '❌ Gagal menghapus: ' . $e->getMessage());
        }
    }

    /**
     * Cari peserta berdasarkan NIS (untuk auto-fill)
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
     * Show the import form
     */
    public function importForm()
    {
        return view('peserta.import');
    }

    /**
     * Import data from Excel
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

            // Skip header
            array_shift($rows);

            $success = 0;
            $errors = [];
            $duplicates = [];

            foreach ($rows as $row) {
                $nis = trim($row[0] ?? '');
                $nama = trim($row[1] ?? '');
                $lembaga = trim($row[2] ?? '');
                $gugus = trim($row[3] ?? '');

                if (empty($nis) || empty($nama)) {
                    $errors[] = "Data tidak lengkap: NIS '$nis', Nama '$nama'";
                    continue;
                }

                if (!in_array($lembaga, ['MTs', 'MA'])) {
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
            if (count($errors) > 0) {
                $message .= " ❌ " . count($errors) . " data gagal diimport.";
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
     * Download template Excel
     */
    public function exportTemplate()
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Header
            $sheet->setCellValue('A1', 'NIS');
            $sheet->setCellValue('B1', 'Nama Lengkap');
            $sheet->setCellValue('C1', 'Lembaga (MTs/MA)');
            $sheet->setCellValue('D1', 'Gugus/Kelompok');

            // Style header
            $headerStyle = $sheet->getStyle('A1:D1');
            $headerStyle->getFont()->setBold(true)->setSize(12);
            $headerStyle->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF1E3A8A');
            $headerStyle->getFont()->getColor()->setARGB('FFFFFFFF');

            // Example data
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

            // Auto size
            foreach (range('A', 'D') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Border
            $styleArray = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCCCCCC'],
                    ],
                ],
            ];
            $sheet->getStyle('A1:D4')->applyFromArray($styleArray);

            $writer = new Xlsx($spreadsheet);
            
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

    // ================================================================
    // 🆕 TAMBAH PESERTA CEPAT DARI KIOSK
    // ================================================================
    public function cepat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nis' => 'required|string|unique:peserta,nis|max:20',
            'nama_lengkap' => 'required|string|max:100',
            'lembaga' => 'required|in:MTs,MA',
            'gugus' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $peserta = Peserta::create([
                'nis' => $request->nis,
                'nama_lengkap' => $request->nama_lengkap,
                'lembaga' => $request->lembaga,
                'gugus' => $request->gugus,
            ]);

            return response()->json([
                'success' => true,
                'message' => '✅ Peserta berhasil didaftarkan!',
                'data' => $peserta
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Gagal mendaftarkan: ' . $e->getMessage()
            ], 500);
        }
    }

    // ================================================================
    // 🗑️ HAPUS SEMUA DATA (MASS DELETE)
    // ================================================================
    public function hapusSemua(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return redirect()->route('peserta.index')->with('error', '❌ Password harus diisi!');
        }

        $user = Auth::user();
        if (!Hash::check($request->password, $user->password)) {
            return redirect()->route('peserta.index')->with('error', '❌ Password salah!');
        }

        try {
            DB::beginTransaction();

            $absensiCount = Absensi::count();
            $pesertaCount = Peserta::count();

            // Matikan foreign key check
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Hapus semua data
            Absensi::truncate();
            Peserta::truncate();

            // Nyalakan kembali foreign key check
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            // Reset auto-increment
            DB::statement('ALTER TABLE absensi AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE peserta AUTO_INCREMENT = 1');

            DB::commit();

            return redirect()->route('peserta.index')->with('success', 
                "✅ Berhasil menghapus {$pesertaCount} data peserta dan {$absensiCount} data absensi!"
            );

        } catch (\Exception $e) {
            DB::rollBack();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            return redirect()->route('peserta.index')->with('error', '❌ Gagal menghapus data: ' . $e->getMessage());
        }
    }
}