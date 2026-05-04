<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Petani - UrbanFarm</title>
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f8fafc; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: white; padding: 30px; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.05); width: 100%; max-width: 450px; border: 1px solid #e2e8f0; }
        h1 { color: #1e293b; font-size: 28px; margin-top: 0; margin-bottom: 10px; text-align: center; font-weight: 800; }
        .subtitle { text-align: center; color: #64748b; font-size: 15px; margin-bottom: 25px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: 700; margin-bottom: 8px; color: #334155; font-size: 14px; }
        input { width: 100%; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 12px; box-sizing: border-box; transition: 0.3s; font-family: inherit; }
        input:focus { border-color: #10b981; outline: none; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); }
        .alert-error { background: #fef2f2; color: #dc2626; padding: 12px; border-radius: 12px; border: 1px solid #fee2e2; margin-bottom: 20px; font-size: 14px; text-align: center; font-weight: 600; }
        .section-confirm { background: #fffbeb; padding: 20px; border-radius: 16px; border: 1px dashed #f59e0b; margin-top: 25px; }
        .btn-update { background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 14px; border: none; border-radius: 12px; cursor: pointer; font-weight: 800; width: 100%; margin-top: 25px; font-size: 16px; transition: 0.3s; box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2); }
        .btn-update:hover { transform: translateY(-2px); box-shadow: 0 15px 25px rgba(16, 185, 129, 0.3); }
        .btn-cancel { display: block; text-align: center; margin-top: 15px; color: #64748b; text-decoration: none; font-size: 14px; font-weight: 600; }
        .logo-preview { width: 80px; height: 80px; border-radius: 16px; object-cover: cover; margin-bottom: 10px; border: 2px solid #e2e8f0; }
        .file-input-wrapper { position: relative; display: flex; flex-direction: column; align-items: center; gap: 10px; padding: 15px; border: 2px dashed #e2e8f0; border-radius: 16px; cursor: pointer; transition: 0.3s; }
        .file-input-wrapper:hover { border-color: #10b981; background: #f0fdf4; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Edit Profil Petani</h1>
        <p class="subtitle">Perbarui informasi identitas petani kota Anda.</p>

        @if(session('error'))
            <div class="alert-error">⚠️ {{ session('error') }}</div>
        @endif

        <form action="{{ route('user.update', $user->id_user) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label>Foto Profil Anda:</label>
                <div class="file-input-wrapper" onclick="document.getElementById('logo').click()">
                    <img id="preview" src="{{ $user->logo_path ? asset('storage/' . $user->logo_path) : 'https://cdn-icons-png.flaticon.com/512/628/628283.png' }}" class="logo-preview" style="border-radius: 50%;">
                    <span style="font-size: 12px; color: #64748b; font-weight: 600;">Klik untuk ganti foto profil (PNG/JPG)</span>
                    <input type="file" id="logo" name="logo" style="display: none;" onchange="previewImage(this)">
                </div>
            </div>

            <div class="form-group">
                <label for="nama">Nama Lengkap:</label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}" required placeholder="Contoh: Andi Wijaya">
            </div>
            
            <div class="form-group">
                <label for="email">Alamat Email:</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="email@contoh.com">
            </div>

            <div class="section-confirm">
                <label style="color: #d35400;">Konfirmasi Keamanan:</label>
                <p style="color: #e67e22; font-size: 11px; margin-bottom: 10px;">Wajib masukkan password akun Anda untuk menyimpan perubahan.</p>
                <input type="password" name="password_konfirmasi" required placeholder="Masukkan password Anda" style="border: 1px solid #f39c12;">
            </div>

            <button type="submit" class="btn-update">Simpan Perubahan</button>
            <a href="{{ route('user.index') }}" class="btn-cancel">Batal dan Kembali</a>
        </form>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>