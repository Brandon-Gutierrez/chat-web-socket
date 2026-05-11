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
    
    <header class="bg-white px-6 py-4 flex justify-between items-center shadow-sm z-10">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-indigo-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div class="flex flex-col">
                <span class="font-semibold text-gray-800 text-lg">{{ $chat->name }}</span>
                <span class="text-xs text-gray-500" id="online-count">Conectando...</span>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <button onclick="copyInviteLink()" class="text-gray-500 hover:text-gray-800" title="Copiar ID del chat">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
            </button>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="flex items-center gap-2 text-gray-600 hover:text-gray-900 text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </header>

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
                        <svg class="w-5 h-5 -rotate-45 translate-y-[-1px] translate-x-[1px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
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
        const userName = @json(auth()->user()->name);
        const messagesContainer = document.getElementById('messages-container');
        let onlineUsers = [];

        // Scroll al fondo al cargar
        messagesContainer.scrollTop = messagesContainer.scrollHeight;

        function copyInviteLink() {
            navigator.clipboard.writeText(chatId);
            alert('ID de sala copiado: ' + chatId + '\nCompártelo para que otros se unan.');
        }

        // WebSockets con Laravel Echo (Reverb)
        window.Echo.join(`chat.${chatId}`)
            .here((users) => {
                onlineUsers = users;
                updateUsersUI();
            })
            .joining((user) => {
                onlineUsers.push(user);
                updateUsersUI();
                renderSystemMessage(`${user.name} se unió al chat`, 'join');
            })
            .leaving((user) => {
                onlineUsers = onlineUsers.filter(u => u.id !== user.id);
                updateUsersUI();
                renderSystemMessage(`${user.name} abandonó el chat`, 'leave');
            })
            .listen('MessageSent', (e) => {
                renderMessage(e.message, false);
            });

        // Enviar Mensaje
        const input = document.getElementById('message-input');
        const sendBtn = document.getElementById('send-btn');

        const sendMessage = async () => {
            const content = input.value;
            if (!content.trim()) return;

            input.value = '';
            
            // Render optimista local
            const tempMsg = {
                content: content,
                user_id: userId,
                created_at: new Date().toISOString()
            };
            renderMessage(tempMsg, true);

            try {
                await axios.post(`/chat/${chatId}/message`, { content }, {
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
            } catch (error) {
                console.error("Error enviando el mensaje", error);
            }
        };

        sendBtn.addEventListener('click', sendMessage);
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendMessage();
        });

        // Funciones de UI
        function updateUsersUI() {
            const countText = `${onlineUsers.length} usuarios conectados`;
            document.getElementById('online-count').innerText = countText;
            document.getElementById('online-count-sidebar').innerText = countText;
            
            const list = document.getElementById('users-list');
            list.innerHTML = onlineUsers.map(u => `
                <li class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#8B5CF6] text-white flex items-center justify-center font-bold text-sm">
                        ${u.name.charAt(0).toUpperCase()}
                        <div class="absolute w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full translate-x-3 translate-y-3"></div>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-medium text-gray-800">${u.name}</span>
                        <span class="text-[10px] text-green-500 font-medium">En línea</span>
                    </div>
                </li>
            `).join('');
        }

        function renderMessage(msg, isMine) {
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
