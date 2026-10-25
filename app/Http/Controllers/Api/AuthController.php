<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'       => 'required|email',
            'password'    => 'required',
            'device_name' => 'nullable|string|max:100',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => 'Email atau password salah.']);
        }

        return response()->json([
            'token' => $user->createToken($data['device_name'] ?? 'android')->plainTextToken,
            'user'  => $this->tampil($user),
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($this->tampil($request->user()));
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Berhasil keluar.']);
    }

    private function tampil(User $u): array
    {
        return ['id_user' => $u->id_user, 'nama_user' => $u->nama_user, 'email' => $u->email, 'role' => $u->role];
    }
}
