<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\SpaceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\AiController;

// --- 1. HALAMAN PUBLIK ---
Route::get('/', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/', [RegisterController::class, 'register']);

Route::get('/login', function() { return view('auth.login'); })->name('login');
Route::post('/login', function(Illuminate\Http\Request $request) {
    $credentials = $request->only('email', 'password');
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('dashboard');
    }
    return back()->with('error', 'Email atau password salah!');
});

// --- 2. HALAMAN PRIVATE (Auth) ---
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Lahan & Katalog tetap pakai Resource karena butuh CRUD lengkap
    Route::resource('lahan', SpaceController::class);
    Route::get('/lahan/rekomendasi/{id}', [SpaceController::class, 'rekomendasi'])->name('lahan.rekomendasi');
    Route::resource('katalog', KatalogController::class);
    Route::get('/katalog/{id}/ai-lifecycle', [KatalogController::class, 'getAiLifecycle'])->name('katalog.lifecycle');
    Route::delete('/lahan/{id}', [SpaceController::class, 'destroy'])->name('lahan.destroy');

    // User pakai manual agar sesuai dengan fungsi "Self-Edit" kita
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/user/{id}', [UserController::class, 'update'])->name('user.update');

    // Chat Routes
    Route::get('/chat/messages', [\App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [\App\Http\Controllers\ChatController::class, 'store'])->name('chat.store');
    Route::delete('/chat/clear', [\App\Http\Controllers\ChatController::class, 'clear'])->name('chat.clear');

    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});

Route::get('/semua-jadwal', [JadwalController::class, 'index'])->name('jadwal.index');

Route::post('/simpan-tanam', [DashboardController::class, 'simpanTanam'])->name('simpan.tanam');
Route::post('/sync-katalog-ai', [DashboardController::class, 'syncKatalogAi'])->name('sync.katalog.ai');
Route::delete('/semua-jadwal/{id}', [DashboardController::class, 'hapusJadwal'])->name('jadwal.destroy');

Route::post('/complete-task/{id}', [JadwalController::class, 'completeTask'])->name('complete.task');
Route::delete('/semua-jadwal/{id}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');
Route::post('/jadwal/selesai', [JadwalController::class, 'tandaiSelesai'])->name('jadwal.selesai');

Route::get('/ai-assistant', [AiController::class, 'index'])->name('ai.index');
Route::post('/ai-ask', [AiController::class, 'chat'])->name('ai.ask');Route::post('/ai-clear', [AiController::class, 'clear'])->name('ai.clear');

Route::post('/test-notification', function(\Illuminate\Http\Request $request) {
    if(!Auth::check()) return abort(403);
    broadcast(new \App\Events\TaskNotification(Auth::id(), 'Tugas Baru!', 'Waktunya menyiram tanaman Anda.', '/jadwal'));
    return response()->json(['status' => 'ok']);
});

Route::get('/test-notification', function() {
    if(!Auth::check()) return 'Silakan login terlebih dahulu!';
    broadcast(new \App\Events\TaskNotification(Auth::id(), 'Tes Developer!', 'Notifikasi realtime berhasil dipicu secara manual!', '/semua-jadwal'));
    return 'Notifikasi berhasil dikirim! Silakan periksa halaman aplikasi Anda yang sedang terbuka.';
});
