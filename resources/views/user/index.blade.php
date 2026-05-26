@extends('layouts.app')

@section('title', 'Komunitas Petani')

@section('styles')
<style>
    /* Dynamic Mesh Background */
    .mesh-bg {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 0;
        overflow: hidden;
        background-color: #f1f5f9;
        background-image: radial-gradient(rgba(16, 185, 129, 0.2) 2px, transparent 2px);
        background-size: 35px 35px;
        pointer-events: none;
    }
    .blob-1 {
        position: absolute; top: -10%; left: -20%; width: 55vw; height: 55vw;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.3) 0%, rgba(16, 185, 129, 0) 70%);
        filter: blur(80px); animation: float 25s infinite alternate ease-in-out;
    }
    .blob-2 {
        position: absolute; bottom: -15%; right: 10%; width: 65vw; height: 65vw;
        background: radial-gradient(circle, rgba(5, 150, 105, 0.25) 0%, rgba(5, 150, 105, 0) 70%);
        filter: blur(100px); animation: float 30s infinite alternate-reverse ease-in-out;
    }
    .blob-3 {
        position: absolute; top: 30%; left: 35%; transform: translate(-50%, -50%); width: 50vw; height: 50vw;
        background: radial-gradient(circle, rgba(14, 165, 233, 0.2) 0%, rgba(167, 243, 208, 0.15) 40%, rgba(167, 243, 208, 0) 70%);
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
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05), 0 2px 4px rgba(0, 0, 0, 0.03);
        transition: all 0.5s cubic-bezier(0.2, 0.8, 0.2, 1);
    }
    .glass-card-premium:hover {
        background: #ffffff;
        border-color: rgba(16, 185, 129, 0.4);
        box-shadow: 0 12px 25px rgba(16, 185, 129, 0.15), 0 8px 10px rgba(16, 185, 129, 0.05);
        transform: translateY(-5px);
    }
    .glass-card-my-profile {
        background: #ffffff;
        border: 2px solid rgba(16, 185, 129, 0.4);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.12), 0 4px 6px rgba(16, 185, 129, 0.05);
    }
    .glass-card-my-profile:hover {
        border-color: rgba(16, 185, 129, 0.8);
        box-shadow: 0 15px 30px rgba(16, 185, 129, 0.25), 0 10px 15px rgba(16, 185, 129, 0.1);
    }

    /* Avatar Styling */
    .avatar-wrapper {
        position: relative;
        width: 96px;
        height: 96px;
        margin: 0 auto 1.5rem;
    }
    .avatar-glow {
        position: absolute;
        inset: -5px;
        background: linear-gradient(135deg, #10b981, #0ea5e9);
        border-radius: 50%;
        filter: blur(10px);
        opacity: 0.4;
        transition: opacity 0.5s ease;
    }
    .glass-card-premium:hover .avatar-glow { opacity: 0.7; }
    .avatar-inner {
        position: relative;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #10b981, #0d9488);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: 800;
        color: white;
        border: 4px solid white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .glass-card-my-profile .avatar-inner {
        background: linear-gradient(135deg, #0ea5e9, #10b981);
    }

    /* Status Badge */
    .status-badge {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 800;
        border: 1px solid rgba(16, 185, 129, 0.2);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Floating Me Tag */
    .me-tag {
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
        display: flex;
        align-items: center;
        gap: 4px;
        z-index: 10;
    }

    /* Chatbox Styles */
    .chatbox-container {
        position: fixed;
        top: 2rem;
        right: 2rem;
        width: 350px;
        height: 500px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(24px);
        border: 1px solid rgba(16, 185, 129, 0.3);
        border-radius: 1.5rem;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1), 0 10px 20px rgba(16, 185, 129, 0.1);
        display: flex;
        flex-direction: column;
        z-index: 50;
        transition: all 0.3s cubic-bezier(0.2, 0.8, 0.2, 1);
        transform: translateY(0);
        opacity: 1;
    }
    .chatbox-container.closed {
        transform: translateY(-120%);
        opacity: 0;
        pointer-events: none;
    }
    .chatbox-header {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 1.5rem 1.5rem 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 700;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
    }
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    .chat-message {
        max-width: 85%;
        padding: 0.75rem 1rem;
        border-radius: 1rem;
        font-size: 0.875rem;
        line-height: 1.4;
        position: relative;
    }
    .chat-message.self {
        align-self: flex-end;
        background: #10b981;
        color: white;
        border-bottom-right-radius: 0.25rem;
    }
    .chat-message.other {
        align-self: flex-start;
        background: #f1f5f9;
        color: #334155;
        border-bottom-left-radius: 0.25rem;
        border: 1px solid #e2e8f0;
    }
    .chat-sender {
        font-size: 0.7rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
        opacity: 0.8;
    }
    .chat-input-area {
        padding: 1rem;
        border-top: 1px solid rgba(16, 185, 129, 0.1);
        display: flex;
        gap: 0.5rem;
        background: white;
        border-radius: 0 0 1.5rem 1.5rem;
    }
    .chat-input {
        flex: 1;
        border: 1px solid #cbd5e1;
        border-radius: 2rem;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        outline: none;
        transition: border-color 0.3s;
    }
    .chat-input:focus {
        border-color: #10b981;
    }
    .chat-send-btn {
        background: #10b981;
        color: white;
        border: none;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.3s, transform 0.2s;
    }
    .chat-send-btn:hover {
        background: #059669;
        transform: scale(1.05);
    }
    .chat-toggle-btn {
        position: fixed;
        top: 2rem;
        right: 2rem;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
        cursor: pointer;
        z-index: 40;
        transition: transform 0.3s;
    }
    .chat-toggle-btn:hover {
        transform: scale(1.1);
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

<div class="relative z-10 flex flex-col gap-10 pb-16">
    <!-- Header -->
    <div class="flex flex-col justify-center items-start animate-slide-up">
        <h1 class="text-4xl md:text-5xl font-extrabold text-slate-800 tracking-tight mb-3 flex items-center gap-4">
            <div class="p-3 bg-white/40 backdrop-blur-md rounded-2xl shadow-sm border border-white/50">
                <i data-lucide="users" class="w-8 h-8 md:w-10 md:h-10 text-emerald-600"></i>
            </div>
            Komunitas Petani Digital
        </h1>
        <p class="text-slate-500 font-medium text-lg max-w-2xl">Terhubung, belajar, dan berkolaborasi dengan jaringan agronomis modern yang menggunakan ekosistem UrbanFarm.</p>
    </div>

    <!-- Alert Notification -->
    @if(session('success'))
        <div class="animate-slide-up bg-emerald-50/80 backdrop-blur-md border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3 shadow-sm">
            <div class="bg-emerald-100 text-emerald-600 p-1.5 rounded-full">
                <i data-lucide="check" class="w-5 h-5"></i>
            </div>
            <span class="font-bold tracking-wide">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-8 animate-slide-up" style="animation-delay: 0.1s;">
        @forelse($users as $u)
            <div class="glass-card-premium rounded-[2rem] p-8 relative flex flex-col items-center group overflow-hidden {{ $u->id_user == Auth::id() ? 'glass-card-my-profile' : '' }} user-card" {!! $loop->iteration > 8 ? 'style="display: none;"' : '' !!}>
                
                @if($u->id_user == Auth::id())
                    <div class="me-tag">
                        <i data-lucide="user-check" class="w-3.5 h-3.5"></i> Saya
                    </div>
                    <!-- Decorative Background for My Profile -->
                    <div class="absolute -top-20 -right-20 w-40 h-40 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
                @endif
                
                <!-- Avatar -->
                <div class="avatar-wrapper mt-4">
                    <div class="avatar-glow"></div>
                    @if($u->logo_path)
                        <img src="{{ asset('storage/' . $u->logo_path) }}" class="w-full h-full rounded-full object-cover border-4 border-white shadow-md relative z-10">
                    @else
                        <div class="avatar-inner">
                            {{ strtoupper(substr($u->nama, 0, 1)) }}
                        </div>
                    @endif
                </div>
                
                <!-- User Info -->
                <h3 class="text-xl font-extrabold text-slate-800 mb-1 group-hover:text-emerald-700 transition-colors">{{ $u->nama }}</h3>
                <p class="text-slate-500 font-medium text-sm mb-6">{{ $u->email }}</p>
                
                <div class="status-badge mb-2">
                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></div>
                    Petani Aktif
                </div>

                @if($u->id_user == Auth::id())
                    <div class="mt-8 w-full">
                        <a href="{{ route('user.edit', $u->id_user) }}" class="flex items-center justify-center gap-2 px-5 py-3 w-full bg-white/60 hover:bg-white backdrop-blur-md border border-slate-200 hover:border-emerald-300 text-slate-600 hover:text-emerald-700 rounded-xl transition-all duration-300 font-bold shadow-sm group/btn">
                            <i data-lucide="settings" class="w-4 h-4 transition-transform duration-500 group-hover/btn:rotate-90"></i>
                            <span>Pengaturan Akun</span>
                        </a>
                    </div>
                @endif
            </div>
        @empty
            <div class="glass-card-premium rounded-[2rem] col-span-full text-center p-16">
                <div class="w-20 h-20 bg-emerald-50/50 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-6 border border-emerald-100">
                    <i data-lucide="users-2" class="w-10 h-10 text-emerald-300"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-700 mb-2">Belum Ada Komunitas</h3>
                <p class="text-slate-500 font-medium">Jaringan ekspansi petani UrbanFarm masih kosong saat ini.</p>
            </div>
        @endforelse
    </div>

    @if(count($users) > 8)
    <div class="flex justify-center mt-6 animate-slide-up" id="showMoreUsersContainer" style="animation-delay: 0.2s;">
        <button id="showMoreUsersBtn" class="px-8 py-3 bg-white/70 hover:bg-white backdrop-blur-md border-2 border-emerald-200 text-emerald-700 font-bold rounded-2xl shadow-sm transition-all hover:shadow-md hover:-translate-y-1 flex items-center gap-2 group">
            <span>Lihat Lainnya</span>
            <i data-lucide="chevron-down" class="w-5 h-5 group-hover:translate-y-1 transition-transform"></i>
        </button>
    </div>
    @endif
</div>

<!-- Chat Toggle Button -->
<div class="chat-toggle-btn" id="chatToggleBtn">
    <i data-lucide="message-circle" class="w-7 h-7"></i>
</div>

<!-- Chatbox -->
<div class="chatbox-container closed" id="chatbox">
    <div class="chatbox-header">
        <div class="flex items-center gap-2">
            <i data-lucide="messages-square" class="w-5 h-5"></i>
            <span>Live Community Chat</span>
        </div>
        <div class="flex items-center gap-3">
            <button id="clearChatBtn" class="hover:text-rose-200 transition-colors" title="Hapus Semua Riwayat">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
            <button id="closeChatBtn" class="hover:text-emerald-200 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
    </div>
    <div class="chat-messages" id="chatMessages">
        <!-- Messages will be loaded here -->
    </div>
    <div class="chat-input-area">
        <input type="text" id="chatInput" class="chat-input" placeholder="Tulis pesan..." autocomplete="off">
        <button id="sendChatBtn" class="chat-send-btn">
            <i data-lucide="send" class="w-4 h-4 ml-0.5"></i>
        </button>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        const userId = {{ Auth::id() }};
        const chatbox = $('#chatbox');
        const chatToggleBtn = $('#chatToggleBtn');
        const closeChatBtn = $('#closeChatBtn');
        const chatMessages = $('#chatMessages');
        const chatInput = $('#chatInput');
        const sendChatBtn = $('#sendChatBtn');
        const clearChatBtn = $('#clearChatBtn');

        let isChatLoaded = false;

        // Show More Users Logic
        const showMoreUsersBtn = $('#showMoreUsersBtn');
        const showMoreUsersContainer = $('#showMoreUsersContainer');
        if (showMoreUsersBtn.length) {
            showMoreUsersBtn.on('click', function() {
                $('.user-card:hidden').fadeIn(400);
                showMoreUsersContainer.fadeOut(300);
            });
        }

        // Toggle Chatbox
        chatToggleBtn.on('click', function() {
            chatbox.removeClass('closed');
            if (!isChatLoaded) {
                loadMessages();
                isChatLoaded = true;
            } else {
                scrollToBottom();
            }
        });

        closeChatBtn.on('click', function() {
            chatbox.addClass('closed');
        });

        // Load Messages
        function loadMessages() {
            chatMessages.html('<div class="text-center text-xs text-slate-400 my-4">Memuat pesan...</div>');
            $.get('/chat/messages', function(messages) {
                chatMessages.empty();
                messages.forEach(msg => {
                    appendMessage(msg);
                });
                scrollToBottom();
            });
        }

        function appendMessage(msg) {
            const isSelf = msg.user_id === userId;
            const alignClass = isSelf ? 'self' : 'other';
            const senderName = isSelf ? 'Anda' : (msg.user ? msg.user.nama : 'Unknown');
            
            const html = `
                <div class="chat-message ${alignClass}">
                    <div class="chat-sender">${senderName}</div>
                    <div class="chat-text">${msg.message}</div>
                </div>
            `;
            chatMessages.append(html);
        }

        function scrollToBottom() {
            chatMessages.scrollTop(chatMessages[0].scrollHeight);
        }

        // Send Message
        function sendMessage() {
            const text = chatInput.val().trim();
            if (!text) return;

            // Optimistic UI update
            const tempMsg = {
                user_id: userId,
                user: { nama: 'Anda' },
                message: text
            };
            appendMessage(tempMsg);
            scrollToBottom();
            chatInput.val('');
            
            $.post('/chat/send', {
                _token: '{{ csrf_token() }}',
                message: text
            }).fail(function() {
                alert('Gagal mengirim pesan');
            });
        }

        sendChatBtn.on('click', sendMessage);
        chatInput.on('keypress', function(e) {
            if (e.which === 13) sendMessage();
        });

        // Clear Chat
        clearChatBtn.on('click', function() {
            if(confirm('Yakin ingin menghapus seluruh riwayat chat komunitas dari tampilan Anda?')) {
                $.ajax({
                    url: '/chat/clear',
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function() {
                        chatMessages.empty();
                    },
                    error: function() {
                        alert('Gagal menghapus riwayat chat');
                    }
                });
            }
        });

        // Laravel Echo listening
        if (window.Echo) {
            window.Echo.channel('community-chat')
                .listen('MessageSent', (e) => {
                    // Ignore if it's our own message because we already appended it optimistically
                    if (e.message.user_id !== userId) {
                        appendMessage(e.message);
                        if (!chatbox.hasClass('closed')) {
                            scrollToBottom();
                        } else {
                            // Optional: add a notification badge on the toggle button
                            chatToggleBtn.addClass('animate-bounce');
                            setTimeout(() => chatToggleBtn.removeClass('animate-bounce'), 3000);
                        }
                    }
                });
        }
    });
</script>
@endsection