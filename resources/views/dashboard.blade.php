<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TRECE - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#EEF2FF] h-screen flex flex-col font-sans">
    <header class="bg-white px-6 py-4 flex justify-between items-center shadow-sm">
        <div class="text-indigo-600 font-bold text-xl">TRECE</div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="flex items-center gap-2 text-gray-600 hover:text-gray-900 text-sm font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Cerrar sesión
            </button>
        </form>
    </header>

    <main class="flex-1 flex items-center justify-center" x-data="{ view: 'menu' }">
        <div class="bg-white rounded-xl shadow-lg p-10 w-full max-w-md">
            
            <div x-show="view === 'menu'" class="flex flex-col items-center">
                <h1 class="text-2xl font-bold text-gray-800 mb-2">¿Qué deseas hacer?</h1>
                <p class="text-gray-500 text-sm mb-8">Crea un nuevo chat o únete a uno existente</p>

                <button @click="view = 'create'" class="w-full bg-[#4F46E5] text-white rounded-lg py-3 flex justify-center items-center gap-2 mb-4 hover:bg-indigo-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Crear un nuevo chat
                </button>
                <button @click="view = 'join'" class="w-full bg-white text-[#4F46E5] border border-[#4F46E5] rounded-lg py-3 flex justify-center items-center gap-2 hover:bg-indigo-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                    Unirse a un chat
                </button>
            </div>

            <div x-show="view === 'create'" style="display: none;">
                <div class="text-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">¿Qué deseas hacer?</h1>
                    <p class="text-gray-500 text-sm">Crea un nuevo chat o únete a uno existente</p>
                </div>
                
                <button @click="view = 'menu'" class="text-sm text-gray-500 hover:text-gray-800 mb-4 flex items-center gap-1">
                    &larr; Volver
                </button>
                <form action="{{ route('chat.store') }}" method="POST">
                    @csrf
                    <label class="block text-sm text-gray-700 mb-2">Nombre del chat</label>
                    <input type="text" name="name" class="w-full border border-[#4F46E5] rounded-lg px-4 py-3 mb-6 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Ej: Chat del trabajo, Amigos, etc." required>
                    <button type="submit" class="w-full bg-[#4F46E5] text-white rounded-lg py-3 hover:bg-indigo-700 transition-colors font-medium">
                        Crear chat
                    </button>
                </form>
            </div>

            <div x-show="view === 'join'" style="display: none;">
                <button @click="view = 'menu'" class="text-sm text-gray-500 hover:text-gray-800 mb-4 flex items-center gap-1">
                    &larr; Volver
                </button>
                <form action="{{ route('chat.join') }}" method="POST">
                    @csrf
                    <label class="block text-sm text-gray-700 mb-2">ID del chat (UUID)</label>
                    <input type="text" name="chat_id" class="w-full border border-[#4F46E5] rounded-lg px-4 py-3 mb-6 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Pega el ID del chat aquí" required>
                    <button type="submit" class="w-full bg-[#4F46E5] text-white rounded-lg py-3 hover:bg-indigo-700 transition-colors font-medium">
                        Unirse
                    </button>
                </form>
            </div>

        </div>
    </main>
</body>
</html>