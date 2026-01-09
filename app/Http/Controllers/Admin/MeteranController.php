<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meteran;
use App\Models\User;
use Illuminate\Http\Request;

class MeteranController extends Controller
{
    public function index()
    {
        $meterans = Meteran::with('user')->get();
        return view('pages.admin.meteran.index', compact('meterans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // PERBAIKAN: Ambil pelanggan yang aktif (tidak soft deleted dan status active)
        $pelanggans = User::where('role', 'pelanggan')
                         ->where('status', 'active') // Ubah dari 'aktif' ke 'active'
                         ->whereNull('deleted_at') // Pastikan tidak soft deleted
                         ->orderBy('name')
                         ->get();
        
        // Generate nomor meteran otomatis
        $nomorMeteran = $this->generateNomorMeteran();
        
        return view('pages.admin.meteran.create', compact('pelanggans', 'nomorMeteran'));
    }

    /**
     * Generate nomor meteran otomatis dengan format 00-1XXXXXX
     */
    private function generateNomorMeteran()
    {
        // Ambil nomor meteran terakhir dengan format 00-1XXXXXX
        $lastMeteran = Meteran::where('nomor_meteran', 'LIKE', '00-1%')
                             ->orderBy('nomor_meteran', 'desc')
                             ->first();

        if ($lastMeteran) {
            // Ambil 6 digit terakhir dari nomor meteran
            $lastNumber = intval(substr($lastMeteran->nomor_meteran, 4)); // Ambil setelah "00-1"
            $nextNumber = $lastNumber + 1;
        } else {
            // Jika belum ada meteran, mulai dari 1
            $nextNumber = 1;
        }

        // Format menjadi 6 digit dengan leading zeros
        $formattedNumber = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        
        return '00-1' . $formattedNumber;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ], [
            'user_id.required' => 'Pelanggan wajib dipilih',
            'user_id.exists' => 'Pelanggan yang dipilih tidak valid'
        ]);

        // PERBAIKAN: Validasi tambahan untuk memastikan user yang dipilih adalah pelanggan aktif
        $selectedUser = User::where('id', $request->user_id)
                           ->where('role', 'pelanggan')
                           ->where('status', 'active')
                           ->whereNull('deleted_at')
                           ->first();

        if (!$selectedUser) {
            return back()->withErrors(['user_id' => 'Pelanggan yang dipilih tidak aktif atau tidak valid'])
                        ->withInput();
        }

        // Generate nomor meteran otomatis
        $nomorMeteran = $this->generateNomorMeteran();

        // Cek apakah nomor meteran sudah ada (untuk antisipasi race condition)
        while (Meteran::where('nomor_meteran', $nomorMeteran)->exists()) {
            $nomorMeteran = $this->generateNomorMeteran();
        }

        Meteran::create([
            'nomor_meteran' => $nomorMeteran,
            'user_id' => $request->user_id
        ]);

        return redirect()->route('admin.meteran.index')
                        ->with('success', 'Meteran berhasil ditambahkan dengan nomor: ' . $nomorMeteran);
    }

    /**
     * Display the specified resource.
     */
    public function show(Meteran $meteran)
    {
        $meteran->load('user');
        return view('pages.admin.meteran.show', compact('meteran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Meteran $meteran)
    {
        // PERBAIKAN: Untuk edit, tampilkan semua pelanggan (aktif dan nonaktif) untuk fleksibilitas
        $pelanggans = User::withTrashed() // Include soft deleted users
                         ->where('role', 'pelanggan')
                         ->orderBy('name')
                         ->get();
        
        return view('pages.admin.meteran.edit', compact('meteran', 'pelanggans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Meteran $meteran)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:aktif,nonaktif'
        ], [
            'user_id.required' => 'Pelanggan wajib dipilih',
            'user_id.exists' => 'Pelanggan yang dipilih tidak valid',
            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status harus aktif atau nonaktif'
        ]);

        // Pada update, nomor meteran tidak berubah
        $meteran->update([
            'user_id' => $request->user_id,
            'status' => $request->status
        ]);

        return redirect()->route('admin.meteran.index')
                        ->with('success', 'Meteran berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meteran $meteran)
    {
        $meteran->delete();
        
        return redirect()->route('admin.meteran.index')
                        ->with('success', 'Meteran berhasil dihapus');
    }
    public function delete(Meteran $meteran)
    {
        $meteran->load('user');
        return view('pages.admin.meteran.delete', compact('meteran'));
    }
}