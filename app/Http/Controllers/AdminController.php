<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;

class AdminController extends Controller
{
    // Tampilkan halaman login admin
    public function showLogin()
    {
        if (session('admin_logged_in')) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    // Proses login admin
    public function authenticate(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = DB::table('users')
            ->where('email', $request->email)
            ->where('role', 'admin')
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            session([
                'admin_logged_in' => true,
                'admin_id'        => $user->id_user,
                'admin_name'      => $user->nama,
                'admin_email'     => $user->email,
            ]);
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah, atau akun ini bukan admin.',
        ])->withInput($request->only('email'));
    }

    // Dashboard Admin
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('admin.dashboard', compact('settings'));
    }

    // Simpan perubahan settings
    public function updateSettings(Request $request)
    {
        $data = $request->except(['_token', '_method']);
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        return back()->with('success', 'Pengaturan teks berhasil disimpan! ✅');
    }

    // Logout admin
    public function logout(Request $request)
    {
        $request->session()->forget(['admin_logged_in', 'admin_id', 'admin_name', 'admin_email']);
        return redirect()->route('admin.login')->with('success', 'Berhasil keluar dari panel admin.');
    }
}
