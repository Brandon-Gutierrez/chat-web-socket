<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TRECE - Iniciar Sesión</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#EEF2FF] h-screen flex flex-col font-sans">
    <header class="bg-white px-6 py-4 flex justify-between items-center shadow-sm">
        <div class="text-indigo-600 font-bold text-xl">TRECE</div>
    </header>

    <main class="flex-1 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-lg p-10 w-full max-w-md flex flex-col items-center">
            <div class="bg-indigo-600 rounded-full w-16 h-16 flex items-center justify-center mb-6">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">ChatApp</h1>
            <p class="text-gray-500 text-sm mb-8">Conéctate y chatea en tiempo real</p>

            <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center gap-3 border border-gray-300 rounded-lg py-3 hover:bg-gray-50 transition-colors">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-5 h-5">
                <span class="text-gray-700 font-medium">Continuar con Google</span>
            </a>
            
            <p class="text-[10px] text-gray-400 mt-6">Al continuar, aceptas los términos y condiciones</p>
        </div>
    </main>
</body>
</html>