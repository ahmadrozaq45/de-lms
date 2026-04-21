<?php

namespace App\Http\Controllers;

use App\Models\User;
//use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Handle Login & Generate Token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required', // Nama perangkat (misal: "iPhone_13" atau "Web_App")
        ]);

        $user = User::where('email', $request->email)->first();

        // Cek user dan verifikasi password
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial yang diberikan salah.'],
            ]);
        }

        // Buat token baru
        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * Handle Logout (Hapus Token)
     */
    public function logout(Request $request)
    {
        // Menghapus token yang sedang digunakan saat ini
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil keluar dan token dihapus'
        ]);
    }
    
    public function show()
    {
        return response()->json(Auth::user());
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8'
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);
        return response()->json(['message' => 'Profile updated', 'user' => $user]);
    }
}
