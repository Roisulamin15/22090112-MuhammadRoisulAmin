@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')

<div class="mb-6 flex justify-between items-center">
    <h2 class="text-xl font-semibold text-gray-700">Daftar User</h2>

    <a href="{{ route('users.create') }}"
       class="px-4 py-2 bg-[#7A1E1E] text-white rounded-lg text-sm hover:bg-[#4B0F0F] transition">
        + Tambah User
    </a>
</div>

{{-- ================= DESKTOP TABLE ================= --}}
<div class="hidden md:block">
    <div class="bg-white rounded-xl shadow overflow-hidden">

        <table class="w-full">

            <thead class="bg-gray-100 text-sm text-gray-600">
                <tr>
                    <th class="p-4 text-left">Nama</th>
                    <th class="p-4 text-left">Email</th>
                    <th class="p-4 text-left">Role</th>
                    <th class="p-4 text-left">Status</th>
                    <th class="p-4 text-left">Aksi</th>
                </tr>
            </thead>

            <tbody class="text-sm text-gray-700">

                @foreach($users as $user)
                <tr class="border-t hover:bg-gray-50 transition">

                    <td class="p-4 font-medium">
                        {{ $user->name }}
                    </td>

                    <td class="p-4">
                        {{ $user->email }}
                    </td>

                    <td class="p-4 capitalize">
                        {{ $user->role }}
                    </td>

                    <td class="p-4">
                        <span class="px-3 py-1 text-xs rounded-full font-medium
                            {{ $user->is_active
                                ? 'bg-green-100 text-green-700'
                                : 'bbg-[#7A1E1E] text-white rounded-lg text-sm hover:bg-[#4B0F0F]' }}">
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>

                    <td class="p-4">
                        <div class="flex gap-2">

                    {{-- RESET PASSWORD --}}
                    <a href="{{ route('users.password.edit', $user->id) }}"
                    class="px-3 py-1  bg-[#7A1E1E] text-white rounded-lg text-sm hover:bg-[#4B0F0F] transition">
                        Reset
                    </a>

                    {{-- TOGGLE STATUS --}}
                    <form method="POST"
                        action="{{ route('users.toggle-status', $user->id) }}"
                        onsubmit="return confirm('Yakin ubah status user ini?')">
                        @csrf
                        @method('PATCH')

                        <button
                            class="px-3 py-1.5 text-xs rounded-lg text-white transition
                            {{ $user->is_active
                                ? 'bg-[#7A1E1E] text-white rounded-lg text-sm hover:bg-[#4B0F0F]'
                                : 'bg-green-600 hover:bg-green-700' }}">

                            {{ $user->is_active ? 'Nonaktif' : 'Aktif' }}

                        </button>

                    </form>

                </div>
                    </td>

                </tr>
                @endforeach

            </tbody>

        </table>

    </div>
</div>

{{-- ================= MOBILE CARD ================= --}}
<div class="md:hidden space-y-4">

    @foreach($users as $user)

    <div class="bg-white rounded-xl shadow p-4 space-y-3">

        <div>
            <p class="text-gray-500 text-xs">Nama</p>
            <p class="font-semibold text-gray-800">{{ $user->name }}</p>
        </div>

        <div>
            <p class="text-gray-500 text-xs">Email</p>
            <p class="text-gray-700">{{ $user->email }}</p>
        </div>

        <div class="flex justify-between items-center">

            <div>
                <p class="text-gray-500 text-xs">Role</p>
                <p class="capitalize font-medium">{{ $user->role }}</p>
            </div>

            <span class="px-3 py-1 text-xs rounded-full font-medium
                {{ $user->is_active
                    ? 'bg-green-100 text-green-700'
                    : 'bg-red-100 text-red-700' }}">
                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>

        </div>

        <div class="flex gap-2 pt-2">

            <a href="{{ route('users.password.edit', $user->id) }}"
               class="flex-1 text-center px-3 py-2 bg-yellow-500 text-white text-sm rounded-lg hover:bg-yellow-600 transition">
                Reset
            </a>

            <form method="POST"
                  action="{{ route('users.toggle-status', $user->id) }}"
                  class="flex-1"
                  onsubmit="return confirm('Yakin ubah status user ini?')">
                @csrf
                @method('PATCH')

                <button class="w-full px-3 py-2 text-sm rounded-lg text-white
                    {{ $user->is_active
                        ? 'bg-red-600 hover:bg-red-700'
                        : 'bg-green-600 hover:bg-green-700' }} transition">

                    {{ $user->is_active ? 'Nonaktif' : 'Aktif' }}

                </button>
            </form>

        </div>

    </div>

    @endforeach

</div>

@endsection