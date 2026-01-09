<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Get search parameter
        $search = $request->get('search');
        
        // Query builder untuk pengguna dengan role 'pelanggan'
        $query = User::withTrashed()->where('role', 'pelanggan');
        
        // Jika ada parameter search, tambahkan kondisi pencarian
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('nik', 'LIKE', '%' . $search . '%')
                  ->orWhere('email', 'LIKE', '%' . $search . '%')
                  ->orWhere('username', 'LIKE', '%' . $search . '%')
                  ->orWhere('alamat', 'LIKE', '%' . $search . '%')
                  ->orWhere('no_hp', 'LIKE', '%' . $search . '%');
            });
        }
        
        // Paginate results
        $users = $query->paginate(10);
        
        // Append search parameter to pagination links
        $users->appends(['search' => $search]);
        
        return view('pages.admin.users.index', compact('users', 'search'));
    }

    public function create()
    {
        return view('pages.admin.users.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'nik' => 'required|string|max:16|unique:users',
                'username' => 'nullable|string|max:255|unique:users',
                'email' => 'nullable|string|email|max:255|unique:users',
                'alamat' => 'nullable|string|max:500',
                'no_hp' => 'nullable|string|max:15',
                'password' => 'required|string|min:8',
                'role' => 'required|in:admin,pelanggan,superuser',
                'status' => 'nullable|in:active,inactive',
            ]);

            // Hash the password
            $validated['password'] = bcrypt($validated['password']);
            
            // Set default status if not provided
            if (!isset($validated['status'])) {
                $validated['status'] = 'active';
            }

            User::create($validated);

            // For AJAX request, return JSON
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Pengguna berhasil ditambahkan'
                ]);
            }

            return redirect()->route('admin.users.index')
                ->with('success', 'Pengguna berhasil ditambahkan');
                
        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function show(User $user)
    {
        // Pastikan user memiliki role pelanggan
        if ($user->role !== 'pelanggan') {
            return redirect()->route('admin.users.index')
                ->with('error', 'User tidak ditemukan atau bukan pelanggan');
        }

        // For AJAX request, return JSON
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'nik' => $user->nik,
                    'username' => $user->username,
                    'email' => $user->email,
                    'alamat' => $user->alamat,
                    'no_hp' => $user->no_hp,
                    'role' => $user->role,
                    'status' => $user->deleted_at ? 'Nonaktif' : 'Aktif',
                    'created_at' => $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-',
                    'updated_at' => $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : '-',
                    'deleted_at' => $user->deleted_at ? $user->deleted_at->format('d/m/Y H:i') : null,
                ]
            ]);
        }

        return view('pages.admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        // For AJAX request, return JSON
        if (request()->wantsJson()) {
            return response()->json($user);
        }
        
        return view('pages.admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'nik' => 'required|string|max:16|unique:users.nik,' . $user->id,
                'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
                'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
                'alamat' => 'nullable|string|max:500',
                'no_hp' => 'nullable|string|max:15',
                'password' => 'nullable|string|min:8',
                'role' => 'required|in:admin,pelanggan,superuser',
                'status' => 'nullable|in:active,inactive'
            ]);

            if (!empty($validated['password'])) {
                $validated['password'] = bcrypt($validated['password']);
            } else {
                unset($validated['password']);
            }

            $user->update($validated);

            // For AJAX request, return JSON
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Pengguna berhasil diperbarui'
                ]);
            }

            return redirect()->route('admin.users.index')
                ->with('success', 'Pengguna berhasil diperbarui');
                
        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete(); // Soft delete

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pengguna berhasil dihapus (dinonaktifkan)'
                ]);
            }

            return redirect()->route('admin.users.index')
                ->with('success', 'Pengguna berhasil dihapus (dinonaktifkan)');
                
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    // PERBAIKAN: Method untuk activate user yang sudah di soft delete
    public function activate($id)
    {
        try {
            // Cari user dengan ID termasuk yang sudah di soft delete
            $user = User::withTrashed()->findOrFail($id);
            
            // Pastikan user benar-benar dalam status soft deleted
            if (!$user->trashed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna sudah dalam status aktif'
                ], 400);
            }
            
            // Restore user (aktivasi kembali)
            $user->restore();
            
            return response()->json([
                'success' => true, 
                'message' => 'Pengguna berhasil diaktifkan'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // PERBAIKAN: Method untuk deactivate user
    public function deactivate($id)
    {
        try {
            // Cari user dengan ID (hanya yang aktif)
            $user = User::findOrFail($id);
            
            // Pastikan user belum di soft delete
            if ($user->trashed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna sudah dalam status nonaktif'
                ], 400);
            }
            
            // Soft delete user (nonaktifkan)
            $user->delete();
            
            return response()->json([
                'success' => true, 
                'message' => 'Pengguna berhasil dinonaktifkan'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Method untuk hard delete (hapus permanen)
    public function forceDelete($id)
    {
        try {
            // Cari user dengan ID termasuk yang sudah di soft delete
            $user = User::withTrashed()->findOrFail($id);
            
            // Hard delete - hapus permanen
            $user->forceDelete();
            
            return response()->json([
                'success' => true, 
                'message' => 'Pengguna berhasil dihapus permanen'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}