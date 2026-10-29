<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }

    public function login(Request $request)
    {
        $cred = $request->validate(['email' => 'required|email', 'password' => 'required']);

        if (Auth::attempt($cred, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }
        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // METHOD KHUSUS API UNTUK ANDROID


public function apiLogin(Request $request)
{
    $cred = $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $cred['email'])->first();

    if (!$user || !Hash::check($cred['password'], $user->password)) {
        return response()->json([
            'status'  => false,
            'message' => 'Email atau password salah.',
        ], 401);
    }

    $token = $user->createToken('android_token')->plainTextToken;

    return response()->json([
        'status'  => true,
        'message' => 'Login Berhasil',
        'token'   => $token,
        'user'    => [
            'id_user'   => $user->id_user,
            'nama_user' => $user->nama_user,
            'email'     => $user->email,
            'role'      => $user->role,
        ],
    ], 200);
}

public function me(Request $request)
{
    return response()->json($request->user());
}

public function apiLogout(Request $request)
{
    $request->user()->currentAccessToken()->delete();
    return response()->json(['status' => true, 'message' => 'Logout berhasil']);
}
}
