<header class="bg-white px-6 py-4 flex justify-between items-center shadow-sm z-10">
    <div class="flex items-center gap-4">
        @if(!empty($backRoute))
            <a href="{{ route($backRoute) }}" class="text-indigo-600 hover:text-indigo-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
        @endif

        <div class="flex flex-col">
            <span class="text-indigo-600 font-bold text-xl">{{ $title ?? 'TRECE' }}</span>
            @if(!empty($subtitle))
                <span class="text-xs text-gray-500" id="{{ $subtitleId ?? '' }}">{{ $subtitle }}</span>
            @endif
        </div>
    </div>

    <div class="flex items-center gap-4">
        @if(!empty($showCopyButton))
            <button onclick="copyInviteLink()" class="text-gray-500 hover:text-gray-800" title="Copiar ID del chat">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
            </button>
        @endif

        @auth
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="flex items-center gap-2 text-gray-600 hover:text-gray-900 text-sm font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Cerrar sesión
                </button>
            </form>
        @endauth
    </div>
</header>
