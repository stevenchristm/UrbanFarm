@extends('layouts.app')

@section('title', 'Asisten AI')

@section('styles')
<style>
    /* Dynamic Mesh Background */
    .mesh-bg {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: -1;
        overflow: hidden;
        background-color: #f8fafc;
        pointer-events: none;
    }
    .blob-1 {
        position: absolute; top: -15%; left: -10%; width: 55vw; height: 55vw;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, rgba(16, 185, 129, 0) 70%);
        filter: blur(80px); animation: float 25s infinite alternate ease-in-out;
    }
    .blob-2 {
        position: absolute; bottom: -20%; right: -10%; width: 65vw; height: 65vw;
        background: radial-gradient(circle, rgba(6, 95, 70, 0.12) 0%, rgba(6, 95, 70, 0) 70%);
        filter: blur(100px); animation: float 30s infinite alternate-reverse ease-in-out;
    }
    .blob-3 {
        position: absolute; top: 30%; left: 45%; transform: translate(-50%, -50%); width: 50vw; height: 50vw;
        background: radial-gradient(circle, rgba(14, 165, 233, 0.12) 0%, rgba(167, 243, 208, 0.1) 40%, rgba(167, 243, 208, 0) 70%);
        filter: blur(90px); animation: float 22s infinite alternate ease-in-out;
    }
    @keyframes float {
        0% { transform: translate(0, 0) scale(1) rotate(0deg); }
        50% { transform: translate(3%, 5%) scale(1.05) rotate(2deg); }
        100% { transform: translate(-3%, -2%) scale(0.95) rotate(-2deg); }
    }

    /* Glassmorphism Classes */
    .glass-card-premium {
        background: #ffffff;
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border: 1px solid #cbd5e1;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08), 0 4px 10px rgba(0, 0, 0, 0.03);
    }
    
    .concave-box {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06), 0 2px 4px rgba(0, 0, 0, 0.02);
    }

    .chat-container {
        height: calc(100vh - 200px);
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    #chat-box {
        flex-grow: 1;
        overflow-y: auto;
        padding: 2rem;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        scroll-behavior: smooth;
    }

    /* Elegant Scrollbar */
    #chat-box::-webkit-scrollbar { width: 6px; }
    #chat-box::-webkit-scrollbar-track { background: transparent; }
    #chat-box::-webkit-scrollbar-thumb { background: rgba(16, 185, 129, 0.2); border-radius: 10px; }
    #chat-box::-webkit-scrollbar-thumb:hover { background: rgba(16, 185, 129, 0.4); }

    .message {
        max-width: 80%;
        padding: 1.2rem 1.8rem;
        font-size: 0.95rem;
        line-height: 1.7;
        position: relative;
        animation: fadeInUp 0.4s cubic-bezier(0.2, 0.8, 0.2, 1);
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .message-user {
        align-self: flex-end;
        background: linear-gradient(135deg, #10b981, #0d9488);
        color: white;
        border-radius: 24px 24px 4px 24px;
        box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.3);
    }

    .message-ai {
        align-self: flex-start;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #334155;
        border-radius: 24px 24px 24px 4px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
    }
    
    /* Markdown Styles inside AI Message */
    .message-ai p { margin-bottom: 0.75rem; }
    .message-ai p:last-child { margin-bottom: 0; }
    .message-ai strong { color: #0f172a; font-weight: 800; }
    .message-ai ul { list-style-type: disc; margin-left: 1.5rem; margin-bottom: 0.75rem; }
    .message-ai ol { list-style-type: decimal; margin-left: 1.5rem; margin-bottom: 0.75rem; }
    .message-ai li { margin-bottom: 0.25rem; }
    .message-ai h1, .message-ai h2, .message-ai h3 { font-weight: 800; color: #0f172a; margin-top: 1rem; margin-bottom: 0.5rem; }

    .message-error {
        align-self: center;
        background: rgba(254, 226, 226, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(252, 165, 165, 0.8);
        color: #b91c1c;
        border-radius: 16px;
        font-weight: 600;
        text-align: center;
        max-width: 90%;
    }

    .chat-input-area {
        display: flex;
        gap: 15px;
        align-items: center;
    }

    #user-input {
        flex-grow: 1;
        background: transparent;
        border: none;
        color: #334155;
        padding: 8px 12px;
        outline: none;
        font-family: inherit;
        font-size: 1rem;
        font-weight: 500;
    }
    
    #user-input::placeholder { color: #94a3b8; }

    .typing-indicator {
        font-size: 0.85rem;
        color: #10b981;
        margin-bottom: 12px;
        display: none;
        font-weight: 700;
        padding-left: 1.5rem;
        align-items: center;
        gap: 8px;
    }

    .chat-image {
        max-width: 100%;
        border-radius: 12px;
        margin-bottom: 12px;
        cursor: pointer;
        transition: 0.3s;
        border: 2px solid rgba(255,255,255,0.3);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .chat-image:hover { transform: scale(1.02); }

    .btn-circle {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        color: #64748b;
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        transition: all 0.3s cubic-bezier(0.2, 0.8, 0.2, 1);
    }

    .btn-circle:hover {
        background: #10b981;
        color: white;
        border-color: #10b981;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
    }

    #image-preview-container {
        display: none;
        position: absolute;
        bottom: 110px;
        left: 40px;
        z-index: 100;
    }

    .preview-box {
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(10px);
        border: 2px solid #10b981;
        padding: 8px;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    /* Shimmer Animation for Send Button */
    @keyframes shimmer {
        0% { transform: translateX(-150%) skewX(-20deg); }
        100% { transform: translateX(250%) skewX(-20deg); }
    }
    .btn-shimmer { position: relative; overflow: hidden; }
    .btn-shimmer::after {
        content: ''; position: absolute; top: 0; left: 0; width: 30%; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.6), transparent);
        transform: translateX(-150%) skewX(-20deg);
        animation: shimmer 2s ease-in-out infinite;
    }
</style>
@endsection

@section('background')
<!-- Mesh Background -->
<div class="mesh-bg">
    <div class="blob-1"></div>
    <div class="blob-2"></div>
    <div class="blob-3"></div>
</div>
@endsection

@section('content')

<div class="relative z-10 flex flex-col gap-6 pb-6 h-full">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 animate-slide-up">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-white/40 backdrop-blur-md rounded-2xl shadow-sm border border-white/50">
                <i data-lucide="brain-circuit" class="w-8 h-8 text-emerald-600"></i>
            </div>
            <div>
                <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Pakar Botani AI</h1>
                <p class="text-slate-500 font-medium text-sm">Konsultasi cerdas mengenai kesehatan tanaman, cuaca, dan strategi budidaya.</p>
            </div>
        </div>
        <button onclick="clearChat()" class="flex items-center gap-2 px-5 py-2.5 bg-white/50 hover:bg-white backdrop-blur-md border border-slate-200 text-slate-600 hover:text-red-500 rounded-xl transition-all duration-300 font-bold shadow-sm group">
            <i data-lucide="rotate-ccw" class="w-4 h-4 transition-transform group-hover:-rotate-180 duration-500"></i>
            <span>Reset Sesi</span>
        </button>
    </div>

    <!-- Chat Interface -->
    <div class="chat-container animate-slide-up" style="animation-delay: 0.1s;">
        <!-- Chat Box -->
        <div id="chat-box" class="glass-card-premium rounded-[2rem]">
            @forelse($history as $c)
                <div class="message message-user">
                    @if($c->image)
                        <img src="{{ asset('storage/' . $c->image) }}" class="chat-image" onclick="window.open(this.src)">
                    @endif
                    {{ $c->message }}
                </div>
                <!-- Save raw text in data attribute for marked.js parsing -->
                <div class="message message-ai markdown-body" data-raw="{{ $c->response }}"></div>
            @empty
                <div class="message message-ai">
                    <strong>Selamat Datang di Hub Konsultasi UrbanFarm.</strong><br><br>
                    Sistem Pakar AI kami siap membantu Anda hari ini. Kirimkan pesan atau unggah foto daun tanaman Anda untuk memulai analisa presisi.
                </div>
            @endforelse
        </div>

        <!-- Typing Indicator -->
        <div id="loading-text" class="typing-indicator">
            <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i> Algoritma sedang melakukan komputasi data...
        </div>

        <!-- Image Preview -->
        <div id="image-preview-container">
            <div class="preview-box relative">
                <img src="" id="image-preview" class="h-28 rounded-xl block object-cover">
                <button onclick="removeImage()" class="absolute -top-3 -right-3 bg-red-500 text-white rounded-full p-1.5 shadow-lg hover:bg-red-600 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>

        <!-- Input Area -->
        <div class="concave-box chat-input-area rounded-[1.5rem] p-3">
            <input type="file" id="file-input" accept="image/*" class="hidden" onchange="previewImage(this)">
            
            <button class="btn-circle shrink-0" onclick="document.getElementById('file-input').click()" title="Lampirkan Gambar Tanaman">
                <i data-lucide="camera" class="w-5 h-5"></i>
            </button>
            
            <input type="text" id="user-input" placeholder="Ketik gejala tanaman atau pertanyaan Anda..." autocomplete="off">
            
            <button onclick="sendChat()" id="btn-send" class="btn-shimmer flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-white font-bold rounded-[1.2rem] transition-all shadow-md hover:shadow-lg shadow-emerald-500/20 shrink-0">
                <i data-lucide="send" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Kirim</span>
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Include Marked.js for Premium Markdown Rendering -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<script>
    const chatBox = document.getElementById('chat-box');
    const userInput = document.getElementById('user-input');
    const btnSend = document.getElementById('btn-send');
    const loadingText = document.getElementById('loading-text');
    const fileInput = document.getElementById('file-input');
    const imagePreviewContainer = document.getElementById('image-preview-container');
    const imagePreview = document.getElementById('image-preview');

    // Setup Marked.js Options
    marked.setOptions({
        breaks: true, // converts \n to <br>
        gfm: true     // GitHub Flavored Markdown
    });

    // Parse existing AI messages
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.markdown-body').forEach(el => {
            if(el.dataset.raw) {
                el.innerHTML = marked.parse(el.dataset.raw);
            }
        });
        scrollToBottom();
    });

    let userLat = null;
    let userLon = null;

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition((position) => {
                userLat = position.coords.latitude;
                userLon = position.coords.longitude;
            });
        }
    }
    getLocation();

    function scrollToBottom() {
        chatBox.scrollTo({ top: chatBox.scrollHeight, behavior: 'smooth' });
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreviewContainer.style.display = 'block';
                scrollToBottom();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeImage() {
        fileInput.value = '';
        imagePreviewContainer.style.display = 'none';
    }

    async function sendChat() {
        let message = userInput.value.trim();
        let file = fileInput.files[0];
        if (!message && !file) return;

        appendMessage('user', message, file);
        userInput.value = '';
        removeImage();

        btnSend.disabled = true;
        loadingText.style.display = 'flex';
        lucide.createIcons();
        scrollToBottom();

        try {
            const formData = new FormData();
            formData.append('message', message);
            if (file) formData.append('image', file);
            if (userLat) formData.append('lat', userLat);
            if (userLon) formData.append('lon', userLon);

            const response = await fetch("{{ route('ai.ask') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                body: formData
            });

            const data = await response.json();
            if (data.response) {
                const isError = data.response.includes('Error Sistem:');
                appendMessage(isError ? 'error' : 'ai', data.response);
            }
        } catch (error) {
            console.error(error);
            appendMessage('error', "Terjadi kesalahan fatal. Mohon periksa koneksi internet Anda atau coba lagi nanti.");
        } finally {
            btnSend.disabled = false;
            loadingText.style.display = 'none';
            scrollToBottom();
        }
    }

    function appendMessage(sender, text, file = null) {
        const msgDiv = document.createElement('div');
        msgDiv.classList.add('message');
        
        if (sender === 'user') msgDiv.classList.add('message-user');
        else if (sender === 'error') msgDiv.classList.add('message-error');
        else msgDiv.classList.add('message-ai');

        if (file) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.classList.add('chat-image');
            img.onclick = () => window.open(img.src);
            msgDiv.appendChild(img);
        }

        if (text) {
            const textContainer = document.createElement('div');
            // If it's an AI message, parse markdown!
            if (sender === 'ai') {
                textContainer.innerHTML = marked.parse(text);
            } else if (sender === 'error') {
                textContainer.innerHTML = `<div class="flex flex-col items-center gap-2"><i data-lucide="alert-triangle" class="w-6 h-6"></i><span>${text}</span></div>`;
            } else {
                textContainer.innerHTML = text.replace(/\n/g, '<br>');
            }
            msgDiv.appendChild(textContainer);
        }

        chatBox.appendChild(msgDiv);
        if (sender === 'error') lucide.createIcons();
        scrollToBottom();
    }

    userInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') sendChat();
    });

    async function clearChat() {
        if (!confirm('Hapus riwayat konsultasi saat ini?')) return;
        try {
            const response = await fetch("{{ route('ai.clear') }}", {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            if (response.ok) {
                chatBox.innerHTML = `
                    <div class="message message-ai">
                        <strong>Riwayat Dibersihkan</strong><br><br>
                        Sistem memori spasial telah direset. Silakan ajukan pertanyaan baru.
                    </div>
                `;
            }
        } catch (error) { console.error(error); }
    }
</script>
@endsection