<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Arsip Surat') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            scroll-behavior: smooth;
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-thumb {
            background: #bdbdbd;
            border-radius: 999px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #9e9e9e;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans overflow-hidden">

<div class="flex h-screen overflow-hidden">

    {{-- ================= SIDEBAR ================= --}}
    <aside class="w-64 bg-[#7A1E1E] text-white fixed left-0 top-0 h-screen flex flex-col shadow-lg">

        {{-- LOGO --}}
        <div class="border-b border-white/20">

            <a href="{{ route('dashboard') }}"
               class="block p-6 text-center hover:bg-[#4B0F0F] transition">

                <img src="{{ asset('image/logo kampus.png') }}"
                     alt="Logo Universitas Harkat Negeri"
                     class="w-16 h-16 mx-auto mb-3">

                <h2 class="font-bold text-lg">
                    Sistem Arsip Surat
                </h2>

                <p class="text-xs text-gray-200">
                    Universitas Harkat Negeri
                </p>

            </a>

        </div>

        {{-- MENU --}}
        <nav class="flex-1 overflow-y-auto p-4 space-y-2">

            <a href="{{ route('dashboard') }}"
               class="block px-4 py-2 rounded transition
               {{ request()->routeIs('dashboard') ? 'bg-[#4B0F0F]' : 'hover:bg-[#4B0F0F]' }}">
                Dashboard
            </a>

            <a href="{{ route('surat-masuk.index') }}"
               class="block px-4 py-2 rounded transition
               {{ request()->routeIs('surat-masuk.*') ? 'bg-[#4B0F0F]' : 'hover:bg-[#4B0F0F]' }}">
                Surat Masuk
            </a>

            <a href="{{ route('surat-keluar.index') }}"
               class="block px-4 py-2 rounded transition
               {{ request()->routeIs('surat-keluar.index') ? 'bg-[#4B0F0F]' : 'hover:bg-[#4B0F0F]' }}">
                Surat Keluar
            </a>

            <a href="{{ route('surat-keluar.create') }}"
               class="block px-4 py-2 rounded transition
               {{ request()->routeIs('surat-keluar.create') ? 'bg-[#4B0F0F]' : 'hover:bg-[#4B0F0F]' }}">
                Kirim Surat
            </a>

            @if(auth()->check() && auth()->user()->role === 'admin')

                <div class="pt-4 mt-4 border-t border-white/20">

                    <p class="px-2 mb-2 text-xs uppercase tracking-wider text-gray-300">
                        Manajemen
                    </p>

                    <button type="button"
                            onclick="document.getElementById('userMenu').classList.toggle('hidden')"
                            class="w-full flex justify-between items-center px-4 py-2 rounded hover:bg-[#4B0F0F] transition">

                        <span>Manajemen User</span>

                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M19 9l-7 7-7-7"/>
                        </svg>

                    </button>

                    <div id="userMenu" class="hidden mt-2 ml-4 space-y-1">

                        <a href="{{ route('users.index') }}"
                           class="block px-3 py-2 rounded hover:bg-[#4B0F0F] transition">
                            List User
                        </a>

                        <a href="{{ route('users.create') }}"
                           class="block px-3 py-2 rounded hover:bg-[#4B0F0F] transition">
                            Tambah User
                        </a>

                    </div>

                </div>

            @endif

        </nav>

        {{-- LOGOUT --}}
        <div class="p-4 border-t border-white/20">

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button class="w-full py-2 rounded bg-[#4B0F0F] hover:bg-black transition">
                    Logout
                </button>

            </form>

        </div>

    </aside>

    {{-- ================= MAIN CONTENT ================= --}}
    <main class="flex-1 ml-64 flex flex-col h-screen">

        {{-- NAVBAR --}}
        <div class="bg-white border-b shadow-sm sticky top-0 z-30">

            <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

                <h1 class="text-lg font-semibold text-gray-700">
                    @yield('title')
                </h1>

                <a href="{{ route('profile') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 transition">

                    <div class="w-9 h-9 rounded-full bg-[#7A1E1E]
                                flex items-center justify-center
                                text-white font-bold">

                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}

                    </div>

                    <span class="text-sm font-medium text-gray-700">
                        {{ auth()->user()->name }}
                    </span>

                </a>

            </div>

        </div>

        {{-- CONTENT --}}
        <div class="flex-1 overflow-y-scroll bg-gray-100">

            <div class="max-w-7xl mx-auto px-6 py-6 min-h-[calc(100vh-80px)]">

                <div class="space-y-4">

                    @if(session('error'))
                        <div class="p-4 rounded-xl border border-red-300 bg-red-50 text-red-700 shadow-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="p-4 rounded-xl border border-green-300 bg-green-50 text-green-700 shadow-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    @yield('content')

                </div>

            </div>

        </div>

    </main>

</div>

</body>
</html>