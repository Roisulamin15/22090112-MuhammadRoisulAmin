<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Arsip Surat') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">

<div class="flex min-h-screen">

    {{-- ================= SIDEBAR ================= --}}
    <aside class="w-64 bg-[#7A1E1E] text-white flex flex-col">

        {{-- LOGO --}}
     <div class="p-6 border-b border-white/20 text-center">
        <a href="{{ route('dashboard') }}"
   class="block p-6 border-b border-white/20 text-center hover:bg-[#4B0F0F] transition">

    <img src="{{ asset('image/logo kampus.png') }}"
         alt="Logo Universitas Harkat Negeri"
         class="w-16 h-16 mx-auto mb-3">

    <h2 class="font-bold text-lg leading-tight">
        Sistem Arsip Surat
    </h2>

    <p class="text-xs text-gray-200">
        Universitas Harkat Negeri
    </p>
</a>

</div>

        {{-- MENU UTAMA --}}
        <nav class="flex-1 p-4 space-y-2">
            <a href="{{ route('dashboard') }}"
               class="block px-4 py-2 rounded hover:bg-[#4B0F0F]">
                Dashboard
            </a>

            <a href="{{ route('surat-masuk.index') }}"
               class="block px-4 py-2 rounded hover:bg-[#4B0F0F]">
                Surat Masuk
            </a>

            <a href="{{ route('surat-keluar.index') }}"
               class="block px-4 py-2 rounded hover:bg-[#4B0F0F]">
                Surat Keluar
            </a>

            <a href="{{ route('surat-keluar.create') }}"
               class="block px-4 py-2 rounded hover:bg-[#4B0F0F]">
                Kirim Surat
            </a>
        </nav>

        {{-- ================= MENU ADMIN ================= --}}
        @if(auth()->check() && auth()->user()->role === 'admin')

            <div class="px-4 text-xs uppercase text-gray-300">
                Manajemen
            </div>

            <div class="px-4 mt-2">
                <button
                    type="button"
                    onclick="document.getElementById('userMenu').classList.toggle('hidden')"
                    class="w-full flex justify-between items-center px-3 py-2 rounded hover:bg-[#4B0F0F]">

                    <span>Manajemen User</span>

                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="userMenu" class="hidden mt-2 ml-4 space-y-1">
                    <a href="{{ route('users.index') }}"
                       class="block px-3 py-2 rounded hover:bg-[#4B0F0F]">
                        List User
                    </a>

                    <a href="{{ route('users.create') }}"
                       class="block px-3 py-2 rounded hover:bg-[#4B0F0F]">
                        Tambah User
                    </a>
                </div>
            </div>

        @endif

        {{-- LOGOUT --}}
        <form method="POST" action="{{ route('logout') }}" class="p-4">
            @csrf
            <button class="w-full py-2 rounded bg-[#4B0F0F] hover:bg-black">
                Logout
            </button>
        </form>
    </aside>

    {{-- ================= CONTENT ================= --}}
    <main class="flex-1">

        {{-- NAVBAR --}}
        <div class="bg-white shadow px-6 py-4 flex justify-between items-center">

            <h1 class="font-semibold text-gray-700">
                @yield('title')
            </h1>

            {{-- PROFIL (TANPA DROPDOWN) --}}
            <a href="{{ route('profile') }}"
               class="flex items-center gap-3 hover:bg-gray-100 px-3 py-2 rounded transition">

                <div class="w-9 h-9 rounded-full bg-[#7A1E1E]
                            flex items-center justify-center
                            text-white font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

                <span class="text-sm text-gray-700 font-medium">
                    {{ auth()->user()->name }}
                </span>
            </a>
        </div>

        {{-- PAGE CONTENT --}}
        <div class="p-6">
            @yield('content')
        </div>

    </main>
</div>

</body>
</html>
