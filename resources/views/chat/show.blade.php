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
<body class="bg-[#EEF2FF] h-screen flex flex-col font-sans">
    @include('partials.header', [
        'title' => $chat->name,
        'subtitle' => 'Conectando...',
        'subtitleId' => 'online-count',
        'backRoute' => 'dashboard',
        'showCopyButton' => true,
    ])

    <main class="flex-1 overflow-hidden flex relative bg-white">
        <div class="flex-1 flex flex-col relative">
            <div id="messages-container" class="flex-1 overflow-y-auto p-6 space-y-6 bg-gray-50">
                @foreach($chat->messages as $msg)
                    @if($msg->is_system_message)
                        <div class="flex justify-center">
                            <span class="bg-green-50 text-green-600 px-4 py-1.5 rounded-full text-xs border border-green-200 font-medium shadow-sm">
                                {{ $msg->content }}
                            </span>
                        </div>
                    @else
                        @if($msg->user_id === auth()->id())
                            <div class="flex justify-end">
                                <div class="bg-[#4F46E5] text-white px-5 py-3 rounded-2xl rounded-tr-sm max-w-md shadow-sm">
                                    <p class="text-sm">{{ $msg->content }}</p>
                                    <span class="text-[10px] text-indigo-200 block mt-1 text-right">{{ $msg->created_at->format('H:i') }}</span>
                                </div>
                            </div>
                        @else
                            <div class="flex justify-start flex-col items-start">
                                <span class="text-xs text-gray-400 block mb-1 ml-1">{{ $msg->user->name }}</span>
                                <div class="bg-white border border-gray-200 text-gray-800 px-5 py-3 rounded-2xl rounded-tl-sm max-w-md shadow-sm">
                                    <p class="text-sm">{{ $msg->content }}</p>
                                    <span class="text-[10px] text-gray-400 block mt-1 text-right">{{ $msg->created_at->format('H:i') }}</span>
                                </div>
                            </div>
                        @endif
                    @endif
                @endforeach
            </div>

            <div class="p-4 bg-white border-t border-gray-200">
                <div class="flex items-center gap-3 border border-[#4F46E5] rounded-xl p-1 pr-2 shadow-sm">
                    <input type="text" id="message-input" class="flex-1 bg-transparent px-4 py-2 focus:outline-none text-sm text-gray-700" placeholder="Escribe un mensaje...">
                    <button id="send-btn" class="bg-[#4F46E5] text-white p-2 rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 2 11 13"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m22 2-7 20-4-9-9-4 20-7Z"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <aside class="w-72 bg-white border-l border-gray-200 p-6 hidden lg:block shadow-[0_0_15px_rgba(0,0,0,0.03)] z-10">
            <h3 class="font-semibold text-gray-800 text-lg mb-1">Usuarios en línea</h3>
            <p class="text-xs text-gray-500 mb-6" id="online-count-sidebar">0 conectados</p>
            <ul id="users-list" class="space-y-4">
                </ul>
        </aside>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const chatId = @json((string) $chat->id);
        const userId = @json(auth()->id());
        const renderedMessageIds = new Set(@json($chat->messages->pluck('id')->values()));
        const messagesContainer = document.getElementById('messages-container');
        const input = document.getElementById('message-input');
        const sendBtn = document.getElementById('send-btn');
        const http = window.axios;
        let chatPresence = null;
        let echoInitAttempts = 0;
        let onlineUsers = [];

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
                })
                .leaving((user) => {
                    onlineUsers = onlineUsers.filter(u => u.id !== user.id);
                    updateUsersUI();
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
        window.addEventListener('load', initializeChat);

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
                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-medium text-gray-800">${u.name}</span>
                        <span class="text-[10px] text-green-500 font-medium">En línea</span>
                    </div>
                </li>
            `).join('');
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
                return `<img src="${user.avatar}" alt="${user.name}" class="w-10 h-10 rounded-full object-cover border border-gray-200">`;
            }

            return `<div class="w-10 h-10 rounded-full bg-[#8B5CF6] text-white flex items-center justify-center font-bold text-sm">${user.name.charAt(0).toUpperCase()}</div>`;
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
                    <div class="bg-[#4F46E5] text-white px-5 py-3 rounded-2xl rounded-tr-sm max-w-md shadow-sm">
                        <p class="text-sm">${msg.content}</p>
                        <span class="text-[10px] text-indigo-200 block mt-1 text-right">${time}</span>
                    </div>
                </div>`;
            } else {
                html = `
                <div class="flex justify-start flex-col items-start">
                    <span class="text-xs text-gray-400 block mb-1 ml-1">${msg.user ? msg.user.name : ''}</span>
                    <div class="bg-white border border-gray-200 text-gray-800 px-5 py-3 rounded-2xl rounded-tl-sm max-w-md shadow-sm">
                        <p class="text-sm">${msg.content}</p>
                        <span class="text-[10px] text-gray-400 block mt-1 text-right">${time}</span>
                    </div>
                </div>`;
            }

            messagesContainer.insertAdjacentHTML('beforeend', html);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        function renderSystemMessage(text, type) {
            const colors = type === 'join' ? 'bg-green-50 text-green-600 border-green-200' : 'bg-orange-50 text-orange-600 border-orange-200';
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
