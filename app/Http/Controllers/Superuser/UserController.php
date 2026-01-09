<?php

namespace App\Http\Controllers\Superuser;

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
        
        // Query builder untuk user aktif
        $activeQuery = User::whereNull('deleted_at');
        
        // Query builder untuk user nonaktif  
        $inactiveQuery = User::onlyTrashed();
        
        // Jika ada parameter search, tambahkan kondisi pencarian ke kedua query
        if (!empty($search)) {
            $activeQuery->where(function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('email', 'LIKE', '%' . $search . '%')
                  ->orWhere('username', 'LIKE', '%' . $search . '%')
                  ->orWhere('alamat', 'LIKE', '%' . $search . '%')
                  ->orWhere('no_hp', 'LIKE', '%' . $search . '%');
            });
            
            $inactiveQuery->where(function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('email', 'LIKE', '%' . $search . '%')
                  ->orWhere('username', 'LIKE', '%' . $search . '%')
                  ->orWhere('alamat', 'LIKE', '%' . $search . '%')
                  ->orWhere('no_hp', 'LIKE', '%' . $search . '%');
            });
        }
        
        // Pagination terpisah untuk masing-masing tabel
        $activeUsers = $activeQuery->orderBy('name', 'asc')->paginate(10, ['*'], 'page');
        $inactiveUsers = $inactiveQuery->orderBy('deleted_at', 'desc')->paginate(10, ['*'], 'inactive_page');
        
        // Append search parameter ke pagination links
        if ($search) {
            $activeUsers->appends(['search' => $search, 'inactive_page' => $request->get('inactive_page')]);
            $inactiveUsers->appends(['search' => $search, 'page' => $request->get('page')]);
        }
        
        return view('pages.superuser.users.index', compact('activeUsers', 'inactiveUsers', 'search'));
    }
    
    public function create()
    {
        return view('pages.superuser.users.create');
    }
    
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'nullable|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'alamat' => 'nullable|string|max:500',
                'no_hp' => 'nullable|string|max:15',
                'password' => 'required|string|min:8',
                'role' => 'required|in:admin,pelanggan,superuser,petugas',
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

            return redirect()->route('superuser.users.index')
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
    
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Load user dengan trashed (termasuk yang sudah di soft delete)
        $user = User::withTrashed()->with(['meterans.tagihans', 'pembacaanMeteran.meteran'])->findOrFail($id);
        
        // For AJAX request, return JSON
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        }
        
        return view('pages.superuser.users.show', compact('user'));
    }
    
    public function edit(User $user)
    {
        // For AJAX request, return JSON
        if (request()->wantsJson()) {
            return response()->json($user);
        }
        
        return view('pages.superuser.users.edit', compact('user'));
    }
    
    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'alamat' => 'nullable|string|max:500',
                'no_hp' => 'nullable|string|max:15',
                'password' => 'nullable|string|min:8',
                'role' => 'required|in:admin,pelanggan,superuser,petugas',
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

            return redirect()->route('superuser.users.index')
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

            return redirect()->route('superuser.users.index')
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
    
    // Method untuk activate user yang sudah di soft delete
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

    // Method untuk deactivate user
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