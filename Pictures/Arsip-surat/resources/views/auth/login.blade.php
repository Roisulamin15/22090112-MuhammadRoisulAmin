<x-guest-layout>
    <div class="min-h-screen relative overflow-hidden flex">

        {{-- Background merah tua --}}
        <div class="absolute inset-0 bg-[#7A1E1E]"></div>

        {{-- Area putih kiri --}}
        <div class="relative z-10 w-1/2 bg-white rounded-r-[300px]
                    flex items-center justify-center">
    
            <img src="{{ asset('image/logo kampus.png') }}"
                 alt="Logo Kampus"
                 class="w-56 opacity-90">
        </div>

        {{-- Area kanan (Login) --}}
        <div class="relative z-10 w-1/2 flex items-center justify-center px-10">
            <div class="bg-white shadow-xl rounded-xl w-full max-w-md p-8">

                {{-- Header --}}
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Sign In</h1>
                    <p class="text-sm text-gray-500">
                        Sistem Arsip Surat HMPTI
                    </p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input
                            id="email"
                            class="block mt-1 w-full focus:border-[#7A1E1E] focus:ring-[#7A1E1E]"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autofocus
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <x-input-label for="password" value="Password" />
                        <x-text-input
                            id="password"
                            class="block mt-1 w-full focus:border-[#7A1E1E] focus:ring-[#7A1E1E]"
                            type="password"
                            name="password"
                            required
                        />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember -->
                    <div class="flex items-center justify-between mt-4">
                        <label class="flex items-center">
                            <input type="checkbox"
                                   class="rounded border-gray-300 text-[#7A1E1E] focus:ring-[#7A1E1E]"
                                   name="remember">
                            <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-[#7A1E1E] hover:underline"
                               href="{{ route('password.request') }}">
                                Lupa password?
                            </a>
                        @endif
                    </div>

                    <!-- Button -->
                    <div class="mt-6">
                        <button type="submit"
                                class="w-full py-3 rounded-lg text-white font-semibold
                                       bg-[#7A1E1E] hover:bg-[#4B0F0F] transition">
                            Login
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</x-guest-layout>
