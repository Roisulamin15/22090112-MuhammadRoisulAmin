<form method="POST"
      action="{{ route('password.update') }}"
      class="space-y-5">

    @csrf
    @method('PUT')

    {{-- PASSWORD LAMA --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Password Saat Ini
        </label>
        <input type="password"
               name="current_password"
               class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-[#7A1E1E]/30"
               required>
        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
    </div>

    {{-- PASSWORD BARU --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Password Baru
        </label>
        <input type="password"
               name="password"
               class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-[#7A1E1E]/30"
               required>
        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
    </div>

    {{-- KONFIRMASI --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Konfirmasi Password
        </label>
        <input type="password"
               name="password_confirmation"
               class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-[#7A1E1E]/30"
               required>
    </div>

    {{-- BUTTON --}}
    <div class="flex items-center gap-3 pt-2">
        <button
            class="bg-[#7A1E1E] text-white px-5 py-2 rounded hover:bg-[#4B0F0F]">
            Update Password
        </button>

        @if (session('status') === 'password-updated')
            <span class="text-sm text-green-600">
                Password berhasil diperbarui
            </span>
        @endif
    </div>

</form>
