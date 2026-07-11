<?php

namespace App\Http\Controllers;

use App\Models\Sesi;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SesiController extends Controller
{
    public function index()
    {
        $sesi = Sesi::orderBy('created_at', 'desc')->get();
        $sesiAktif = Sesi::where('is_active', true)->first();
        return view('sesi.index', compact('sesi', 'sesiAktif'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_sesi' => 'required|string|max:100',
            'jam_mulai' => 'required|date_format:H:i',
            'batas_waktu' => 'required|date_format:H:i',
            'peruntukan' => 'required|in:MTs,MA,Semua',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Generate kode sesi otomatis
        $kodeSesi = Sesi::generateKode($request->nama_sesi);

        Sesi::create([
            'kode_sesi' => $kodeSesi,
            'nama_sesi' => $request->nama_sesi,
            'jam_mulai' => $request->jam_mulai . ':00',
            'batas_waktu' => $request->batas_waktu . ':00',
            'is_active' => false,
            'peruntukan' => $request->peruntukan,
        ]);

        return back()->with('success', "✅ Sesi berhasil ditambahkan! Kode: <strong>$kodeSesi</strong>");
    }

    public function update(Request $request, $id)
    {
        $sesi = Sesi::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama_sesi' => 'required|string|max:100',
            'jam_mulai' => 'required|date_format:H:i',
            'batas_waktu' => 'required|date_format:H:i',
            'peruntukan' => 'required|in:MTs,MA,Semua',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $sesi->update([
            'nama_sesi' => $request->nama_sesi,
            'jam_mulai' => $request->jam_mulai . ':00',
            'batas_waktu' => $request->batas_waktu . ':00',
            'peruntukan' => $request->peruntukan,
        ]);

        return response()->json(['success' => true, 'message' => '✅ Sesi berhasil diupdate!']);
    }

    /**
     * Hapus sesi dengan konfirmasi password
     */
    public function destroy(Request $request, $id)
    {
        $sesi = Sesi::findOrFail($id);
        
        // Validasi password
        $validator = Validator::make($request->all(), [
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => '❌ Password harus diisi!'
            ], 422);
        }

        // Cek password
        $user = Auth::user();
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => '❌ Password salah!'
            ], 422);
        }

        // Cek apakah sesi aktif
        if ($sesi->is_active) {
            return response()->json([
                'success' => false,
                'message' => '❌ Tidak bisa menghapus sesi yang sedang aktif! Nonaktifkan dulu.'
            ], 400);
        }

        $namaSesi = $sesi->nama_sesi;
        $kodeSesi = $sesi->kode_sesi;
        
        // Hapus data absensi terkait
        $absensiCount = Absensi::where('sesi_id', $id)->count();
        Absensi::where('sesi_id', $id)->delete();
        
        // Hapus sesi
        $sesi->delete();

        return response()->json([
            'success' => true,
            'message' => "✅ Sesi '$namaSesi' ($kodeSesi) berhasil dihapus! ($absensiCount data absensi terhapus)"
        ]);
    }

    public function toggleActive($id)
    {
        $sesi = Sesi::findOrFail($id);
        
        if (!$sesi->is_active) {
            Sesi::where('is_active', true)->update(['is_active' => false]);
        }
        
        $sesi->is_active = !$sesi->is_active;
        $sesi->save();

        $status = $sesi->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return response()->json([
            'success' => true,
            'message' => "✅ Sesi {$sesi->nama_sesi} ($sesi->kode_sesi) berhasil $status!",
            'is_active' => $sesi->is_active
        ]);
    }
}