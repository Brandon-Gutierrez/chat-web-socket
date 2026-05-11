<header class="bg-[#181818] border-b border-[#2d2d30] px-6 py-4 flex justify-between items-center shadow-sm z-20">
    <div class="flex items-center gap-4">
        @if(!empty($backRoute))
            <a href="{{ route($backRoute) }}" class="text-[#9cdcfe] hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
        @endif

        <div class="flex items-center gap-3 leading-tight">
            <span class="text-[#3794ff] font-bold text-xl">TRECE</span>
            @if(($title ?? 'TRECE') !== 'TRECE')
                <span class="text-[#3c3c3c]">/</span>
                <span class="text-sm text-[#cccccc] font-medium">{{ $title ?? 'TRECE' }}</span>
            @endif
        </div>

        <div class="flex flex-col">
            @if(!empty($subtitle))
                <span class="text-xs text-[#858585]" id="{{ $subtitleId ?? '' }}">{{ $subtitle }}</span>
            @endif
        </div>
    </div>

    <div class="flex items-center gap-4">
        @if(!empty($showCopyButton))
            <button onclick="copyInviteLink()" class="flex items-center gap-2 text-[#cccccc] hover:text-white text-sm font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
            Copiar ID del chat
            </button>

        @endif

        @if(!empty($showUsersToggleButton))
            <button type="button" id="toggle-users-btn" class="inline-flex items-center gap-2 text-[#cccccc] hover:text-white text-sm font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            Usuarios
            </button>

        @endif

        @auth
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="flex items-center gap-2 text-[#cccccc] hover:text-white text-sm font-medium transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Cerrar sesión
                </button>
            </form>
        @endauth
    </div>
</header>
