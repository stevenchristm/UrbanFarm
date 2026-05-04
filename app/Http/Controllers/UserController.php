<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        // Mengurutkan user yang sedang login agar berada di urutan pertama
        $users = User::orderByRaw('CASE WHEN id_user = ? THEN 1 ELSE 2 END', [Auth::id()])
                     ->orderBy('nama', 'asc') // Opsional: urutkan sisanya berdasarkan nama
                     ->get();
                     
        return view('user.index', compact('users'));
    }

    public function edit($id)
    {
        // PENGAMAN: Jika user coba edit ID orang lain lewat URL, lempar error 403
        if ($id != Auth::id()) {
            abort(403, 'Anda hanya boleh mengedit profil Anda sendiri!');
        }

        $user = User::where('id_user', $id)->firstOrFail();
        return view('user.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        if ($id != Auth::id()) {
            abort(403);
        }

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id.',id_user',
            'password_konfirmasi' => 'required',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = User::where('id_user', $id)->firstOrFail();

        // Verifikasi password sebelum update
        if (!Hash::check($request->password_konfirmasi, $user->password)) {
            return redirect()->back()->with('error', 'Password salah! Perubahan ditolak.');
        }

        $updateData = [
            'nama' => $request->nama,
            'email' => $request->email,
        ];

        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($user->logo_path) {
                Storage::disk('public')->delete($user->logo_path);
            }
            $path = $request->file('logo')->store('logos', 'public');
            $updateData['logo_path'] = $path;
        }

        $user->update($updateData);

        return redirect()->route('user.index')->with('success', 'Profil Anda berhasil diperbarui!');
    }
}