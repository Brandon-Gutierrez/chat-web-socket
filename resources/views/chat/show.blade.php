<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TRECE - {{ $chat->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-[#1e1e1e] text-[#d4d4d4] h-screen flex flex-col font-sans">
    @include('partials.header', [
        'title' => $chat->name,
        'subtitle' => 'Conectando...',
        'subtitleId' => 'online-count',
        'backRoute' => 'dashboard',
        'showCopyButton' => true,
        'showUsersToggleButton' => true,
    ])

    <main class="flex-1 overflow-hidden flex relative bg-[#1e1e1e]">
        <div class="flex-1 flex flex-col relative">
            <div id="messages-container" class="chat-scroll flex-1 overflow-y-auto p-6 space-y-6 bg-[#1e1e1e]">
                @foreach($chat->messages as $msg)
                    @if($msg->is_system_message)
                        <div class="flex justify-center">
                            <span class="bg-[#2d2d30] text-[#9cdcfe] px-4 py-1.5 rounded-full text-xs border border-[#3c3c3c] font-medium shadow-sm">
                                {{ $msg->content }}
                            </span>
                        </div>
                    @else
                        @if($msg->user_id === auth()->id())
                            <div class="flex justify-end">
                                <div class="bg-[#0e639c] text-white px-5 py-3 rounded-2xl rounded-tr-sm max-w-md shadow-sm">
                                    <p class="text-sm">{{ $msg->content }}</p>
                                    <span class="text-[10px] text-[#cfe8ff] block mt-1 text-right">{{ $msg->created_at->format('H:i') }}</span>
                                </div>
                            </div>
                        @else
                            <div class="flex justify-start flex-col items-start">
                                <span class="text-xs text-[#858585] block mb-1 ml-1">{{ $msg->user->name }}</span>
                                <div class="bg-[#252526] border border-[#3c3c3c] text-[#d4d4d4] px-5 py-3 rounded-2xl rounded-tl-sm max-w-md shadow-sm">
                                    <p class="text-sm">{{ $msg->content }}</p>
                                    <span class="text-[10px] text-[#858585] block mt-1 text-right">{{ $msg->created_at->format('H:i') }}</span>
                                </div>
                            </div>
                        @endif
                    @endif
                @endforeach
            </div>

            <div class="p-4 bg-[#252526] border-t border-[#2d2d30]">
                <div class="flex items-center gap-3 border border-[#3c3c3c] rounded-xl p-1 pr-2 shadow-sm bg-[#1e1e1e]">
                    <input type="text" id="message-input" class="flex-1 bg-transparent px-4 py-2 focus:outline-none text-sm text-white placeholder:text-[#858585]" placeholder="Escribe un mensaje...">
                    <button id="send-btn" class="bg-[#0e639c] text-white p-2 rounded-lg hover:bg-[#1177bb] transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 2 11 13"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m22 2-7 20-4-9-9-4 20-7Z"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <aside id="users-panel" class="w-72 bg-[#252526] border-l border-[#2d2d30] p-6 shadow-[0_0_15px_rgba(0,0,0,0.18)] z-10 transition-all duration-200">
            <h3 class="font-semibold text-white text-lg mb-1">Conectados</h3>
            <p class="text-xs text-[#858585] mb-6" id="online-count-sidebar">0 conectados</p>
            <ul id="users-list" class="space-y-4">
                </ul>
        </aside>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        .chat-scroll {
            scrollbar-width: thin;
            scrollbar-color: #3c3c3c #1e1e1e;
        }

        .chat-scroll::-webkit-scrollbar {
            width: 10px;
        }

        .chat-scroll::-webkit-scrollbar-track {
            background: #1e1e1e;
        }

        .chat-scroll::-webkit-scrollbar-thumb {
            background: #3c3c3c;
            border-radius: 9999px;
            border: 2px solid #1e1e1e;
        }

        .chat-scroll::-webkit-scrollbar-thumb:hover {
            background: #4f4f52;
        }
    </style>
    <script>
        const chatId = @json((string) $chat->id);
        const userId = @json(auth()->id());
        const renderedMessageIds = new Set(@json($chat->messages->pluck('id')->values()));
        const messagesContainer = document.getElementById('messages-container');
        const input = document.getElementById('message-input');
        const sendBtn = document.getElementById('send-btn');
        const toggleUsersBtn = document.getElementById('toggle-users-btn');
        const usersPanel = document.getElementById('users-panel');
        const http = window.axios;
        let chatPresence = null;
        let echoInitAttempts = 0;
        let onlineUsers = [];
        let usersPanelOpen = window.innerWidth >= 1024;

        function copyInviteLink() {
            navigator.clipboard.writeText(chatId);
            alert('ID de sala copiado: ' + chatId + '\nCompártelo para que otros se unan.');
        }

        function initializeChat() {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;

            if (!window.Echo) {
                echoInitAttempts += 1;

                if (echoInitAttempts < 20) {
                    window.setTimeout(initializeChat, 250);
                    return;
                }

                console.error('Laravel Echo no está disponible.');
                document.getElementById('online-count').innerText = 'Sin conexión en tiempo real';
                document.getElementById('online-count-sidebar').innerText = 'Sin conexión en tiempo real';
                return;
            }

            if (chatPresence) {
                return;
            }

            chatPresence = window.Echo.join(`chat.${chatId}`)
                .here((users) => {
                    onlineUsers = normalizeUsers(users);
                    updateUsersUI();
                })
                .joining((user) => {
                    onlineUsers = normalizeUsers([...onlineUsers, user]);
                    updateUsersUI();
                    renderSystemMessage(`${user.name} se conectó`, 'join');
                })
                .leaving((user) => {
                    onlineUsers = onlineUsers.filter(u => u.id !== user.id);
                    updateUsersUI();
                    renderSystemMessage(`${user.name} se desconectó`, 'leave');
                })
                .listen('.MessageSent', (e) => {
                    if (Number(e.message.user_id) === Number(userId)) {
                        return;
                    }

                    renderMessage(e.message, false);
                });
        }

        const sendMessage = async () => {
            const content = input.value;
            if (!content.trim() || !http) return;

            input.value = '';

            try {
                const response = await http.post(`/chat/${chatId}/message`, { content }, {
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });

                renderMessage(response.data, true);
            } catch (error) {
                console.error("Error enviando el mensaje", error);
                input.value = content;
            }
        };

        sendBtn.addEventListener('click', sendMessage);
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendMessage();
        });
        if (toggleUsersBtn && usersPanel) {
            toggleUsersBtn.addEventListener('click', () => {
                usersPanelOpen = !usersPanelOpen;
                syncUsersPanel();
            });
        }
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024 && !usersPanelOpen) {
                return;
            }

            if (window.innerWidth >= 1024 && usersPanelOpen) {
                syncUsersPanel();
            }
        });
        window.addEventListener('load', initializeChat);
        window.addEventListener('load', syncUsersPanel);

        // Funciones de UI
        function updateUsersUI() {
            const countText = `${onlineUsers.length} usuarios conectados`;
            document.getElementById('online-count').innerText = countText;
            document.getElementById('online-count-sidebar').innerText = countText;
            
            const list = document.getElementById('users-list');
            list.innerHTML = onlineUsers.map(u => `
                <li class="flex items-center gap-3">
                    <div class="relative shrink-0">
                        ${renderUserAvatar(u)}
                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-[#252526] rounded-full"></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-medium text-[#d4d4d4]">${u.name}</span>
                        <span class="text-[10px] text-green-500 font-medium">Conectado</span>
                    </div>
                </li>
            `).join('');
        }

        function syncUsersPanel() {
            if (!usersPanel || !toggleUsersBtn) {
                return;
            }

            usersPanel.classList.toggle('hidden', !usersPanelOpen);
            toggleUsersBtn.setAttribute('aria-pressed', usersPanelOpen ? 'true' : 'false');
            toggleUsersBtn.classList.toggle('text-white', usersPanelOpen);
            toggleUsersBtn.classList.toggle('text-[#cccccc]', !usersPanelOpen);
        }

        function normalizeUsers(users) {
            const uniqueUsers = new Map();

            users.forEach((user) => {
                uniqueUsers.set(user.id, user);
            });

            return Array.from(uniqueUsers.values());
        }

        function renderUserAvatar(user) {
            if (user.avatar) {
                return `<img src="${user.avatar}" alt="${user.name}" class="w-10 h-10 rounded-full object-cover border border-[#3c3c3c]">`;
            }

            return `<div class="w-10 h-10 rounded-full bg-[#0e639c] text-white flex items-center justify-center font-bold text-sm">${user.name.charAt(0).toUpperCase()}</div>`;
        }

        function renderMessage(msg, isMine) {
            if (msg.id && renderedMessageIds.has(msg.id)) {
                return;
            }

            if (msg.id) {
                renderedMessageIds.add(msg.id);
            }

            const time = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            let html = '';

            if (isMine) {
                html = `
                <div class="flex justify-end">
                    <div class="bg-[#0e639c] text-white px-5 py-3 rounded-2xl rounded-tr-sm max-w-md shadow-sm">
                        <p class="text-sm">${msg.content}</p>
                        <span class="text-[10px] text-[#cfe8ff] block mt-1 text-right">${time}</span>
                    </div>
                </div>`;
            } else {
                html = `
                <div class="flex justify-start flex-col items-start">
                    <span class="text-xs text-[#858585] block mb-1 ml-1">${msg.user ? msg.user.name : ''}</span>
                    <div class="bg-[#252526] border border-[#3c3c3c] text-[#d4d4d4] px-5 py-3 rounded-2xl rounded-tl-sm max-w-md shadow-sm">
                        <p class="text-sm">${msg.content}</p>
                        <span class="text-[10px] text-[#858585] block mt-1 text-right">${time}</span>
                    </div>
                </div>`;
            }

            messagesContainer.insertAdjacentHTML('beforeend', html);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        function renderSystemMessage(text, type) {
            const colors = type === 'join'
                ? 'bg-[#1b2b36] text-[#9cdcfe] border-[#264f78]'
                : 'bg-[#2d2530] text-[#c586c0] border-[#4b3b55]';
            const html = `
            <div class="flex justify-center my-2">
                <span class="${colors} px-4 py-1.5 rounded-full text-xs border font-medium shadow-sm">
                    ${text}
                </span>
            </div>`;
            messagesContainer.insertAdjacentHTML('beforeend', html);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    </script>
</body>
</html>
