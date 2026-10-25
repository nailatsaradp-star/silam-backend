<?php

namespace App\Http\Controllers;

use App\Models\PesananModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderBy('nama_user')->paginate(10);
        return view('hak_akses.admin.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_user' => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:6',
            'role'      => 'required|in:administrator,kasir,pegawai',
        ]);
        User::create($data); // password di-hash otomatis oleh cast 'hashed'
        return back()->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'nama_user' => 'required|string|max:255',
            'email'     => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id_user, 'id_user')],
            'password'  => 'nullable|string|min:6',
            'role'      => 'required|in:administrator,kasir,pegawai',
        ]);
        if (empty($data['password'])) unset($data['password']);
        $user->update($data);
        return back()->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user)
    {
         if ($user->id_user === $request->user()->id_user) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }
        if (PesananModel::where('id_user', $user->id_user)->exists()) {
            return back()->with('error', 'User sudah punya riwayat transaksi, tidak bisa dihapus.');
        }
        $user->delete();
        return back()->with('success', 'User dihapus.');
    }
}
