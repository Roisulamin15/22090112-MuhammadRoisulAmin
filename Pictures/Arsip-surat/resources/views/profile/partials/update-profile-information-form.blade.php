<section>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        {{-- NAMA --}}
        <div>
            <x-input-label for="name" :value="__('Nama')" />

            <x-text-input id="name"
                          name="name"
                          type="text"
                          class="mt-1 block w-full"
                          :value="old('name', $user->name)"
                          required
                          autofocus />

            <x-input-error class="mt-2"
                           :messages="$errors->get('name')" />
        </div>

        {{-- EMAIL --}}
        <div>
            <x-input-label for="email" :value="__('Email')" />

            <x-text-input id="email"
                          name="email"
                          type="email"
                          class="mt-1 block w-full"
                          :value="old('email', $user->email)"
                          required />

            <x-input-error class="mt-2"
                           :messages="$errors->get('email')" />
        </div>

        {{-- NOMOR TELEPON --}}
        <div>
            <x-input-label for="phone" :value="__('Nomor Telepon')" />

            <x-text-input id="phone"
                          name="phone"
                          type="text"
                          class="mt-1 block w-full"
                          :value="old('phone', $user->phone)" />

            <x-input-error class="mt-2"
                           :messages="$errors->get('phone')" />
        </div>

        {{-- BUTTON --}}
        <div class="flex items-center gap-4">
            <x-primary-button>
                Simpan
            </x-primary-button>
        </div>

    </form>

</section>