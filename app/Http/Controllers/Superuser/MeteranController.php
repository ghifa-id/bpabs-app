<?php

namespace App\Http\Controllers\Superuser;

use App\Http\Controllers\Controller;
use App\Models\Meteran;
use App\Models\User;
use Illuminate\Http\Request;

class MeteranController extends Controller
{
    public function index()
    {
        $meterans = Meteran::with('user')->get();
        return view('pages.superuser.meteran.index', compact('meterans'));
    }

    public function create()
    {
        $pelanggans = User::where('role', 'pelanggan')
                        ->where('status', 'active')
                        ->whereNull('deleted_at')
                        ->orderBy('name')
                        ->get();

        $nomorMeteran = $this->generateNomorMeteran();

        return view('pages.superuser.meteran.create', compact('pelanggans', 'nomorMeteran'));
    }

    private function generateNomorMeteran()
    {
        $lastMeteran = Meteran::where('nomor_meteran', 'LIKE', '00-1%')
                        ->orderBy('nomor_meteran', 'desc')
                        ->first();
        
        if ($lastMeteran) {
            $lastNumber = intval(substr($lastMeteran->nomor_meteran, 4));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $formattedNumber = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        return '00-1' . $formattedNumber;
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ], [
            'user_id.required' => 'Pelanggan wajib dipilih',
            'user_id.exists' => 'Pelanggan yang dipilih tidak valid'
        ]);

        $selectedUser = User::where('id', $request->user_id)
                        ->where('role', 'pelanggan')
                        ->where('status', 'active')
                        ->whereNull('deleted_at')
                        ->first();
        
        if (!$selectedUser) {
            return back()->withErrors(['user_id' => 'Pelanggan yang dipilih tidak aktif atau tidak valid'])
                        ->withInput();
        }

        Meteran::create([
            'nomor_meteran' => $nomorMeteran,
            'user_id' => $request->user_id
        ]);

        return redirect()->route('superuser.meteran.index')
                        ->with('success', 'Meteran berhasil ditambahkan dengan nomor:' . $nomorMeteran);
    }

    public function show(Meteran $meteran)
    {
        $meteran->load('user');
        return view('pages.superuser.meteran.show', compact('meteran'));
    }

    public function edit(Meteran $meteran)
    {
        $pelanggans = User::withTrashed()
                        ->where('role', 'pelanggan')
                        ->orderBy('name')
                        ->get();

        return view('pages.superuser.meteran.edit', compact('meteran', 'pelanggans'));
    }

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

        $meteran->update([
            'user_id' => $request->user_id,
            'status' => $request->status
        ]);

        return redirect()->route('superuser.meteran.index')
                        ->with('success', 'Meteran berhasil diperbarui');
    }

    public function destroy(Meteran $meteran)
    {
        $meteran->delete();

        return redirect()->route('superuser.meteran.index')
                        ->with('success', 'Meteran berhasil dihapus');
    }

    public function delete(Meteran $meteran)
    {
        $meteran->load('user');
        return view('pages.superuser.meteran.delete', compact('meteran'));
    }
}

?>