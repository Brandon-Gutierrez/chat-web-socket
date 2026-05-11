<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TRECE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#1e1e1e] text-[#d4d4d4] h-screen flex flex-col font-sans">
    @include('partials.header', ['title' => 'Inicio'])

    <main class="flex-1 flex items-center justify-center" x-data="{ view: 'menu' }">
        <div class="bg-[#252526] border border-[#2d2d30] rounded-xl shadow-lg p-10 w-full max-w-md">
            
            <div x-show="view === 'menu'" class="flex flex-col items-center">
                <h1 class="text-2xl font-bold text-white mb-2">Qué deseas hacer?</h1>
                <p class="text-[#9da5b4] text-sm mb-8">Crea un nuevo chat o únete a uno existente</p>

                <button @click="view = 'create'" class="w-full bg-[#0e639c] text-white rounded-lg py-3 flex justify-center items-center gap-2 mb-4 hover:bg-[#1177bb] transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Crear chat
                </button>
                <button @click="view = 'join'" class="w-full bg-[#2d2d30] text-[#9cdcfe] border border-[#0e639c] rounded-lg py-3 flex justify-center items-center gap-2 hover:bg-[#37373d] transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                    Unirse a un chat
                </button>
            </div>

            <div x-show="view === 'create'" style="display: none;">
                <button @click="view = 'menu'" class="text-sm text-[#9da5b4] hover:text-white mb-4 flex items-center gap-1">
                    &larr; Volver
                </button>
                <form action="{{ route('chat.store') }}" method="POST">
                    @csrf
                    <div class="text-center mb-6">
                        <h1 class="text-2xl font-bold text-white mb-2">CREAR CHAT</h1>
                        <p class="text-[#9da5b4] text-sm">Ingresa el nombre del nuevo chat</p>
                    </div>
                    <label class="block text-sm text-[#d4d4d4] mb-2">Nombre del chat</label>
                    <input type="text" name="name" class="w-full border border-[#3c3c3c] bg-[#1e1e1e] text-white rounded-lg px-4 py-3 mb-6 focus:outline-none focus:ring-1 focus:ring-[#3794ff]" placeholder="Ejemplo: Los turromantikos" required>
                    <button type="submit" class="w-full bg-[#0e639c] text-white rounded-lg py-3 hover:bg-[#1177bb] transition-colors font-medium">
                        Crear chat
                    </button>
                </form>
            </div>

            <div x-show="view === 'join'" style="display: none;">
                <button @click="view = 'menu'" class="text-sm text-[#9da5b4] hover:text-white mb-4 flex items-center gap-1">
                    &larr; Volver
                </button>
                <form action="{{ route('chat.join') }}" method="POST">
                    @csrf
                    <div class="text-center mb-6">
                        <h1 class="text-2xl font-bold text-white mb-2">UNIRSE A CHAT</h1>
                        <p class="text-[#9da5b4] text-sm">Ingresa el ID del chat al que deseas unirte</p>
                    </div>
                    <label class="block text-sm text-[#d4d4d4] mb-2">ID del chat</label>
                    <input type="text" name="chat_id" class="w-full border border-[#3c3c3c] bg-[#1e1e1e] text-white rounded-lg px-4 py-3 mb-6 focus:outline-none focus:ring-1 focus:ring-[#3794ff]" placeholder="Escribe o pega el id del chat aquí" required>
                    <button type="submit" class="w-full bg-[#0e639c] text-white rounded-lg py-3 hover:bg-[#1177bb] transition-colors font-medium">
                        Unirse
                    </button>
                </form>
            </div>

        </div>
    </main>
</body>
</html>
