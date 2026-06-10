@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')

<div class="mb-4 flex justify-between items-center">
    <h2 class="text-lg font-semibold">Daftar User</h2>

    <a href="{{ route('users.create') }}"
       class="px-4 py-2 bg-[#7A1E1E] text-white rounded text-sm hover:bg-[#4B0F0F]">
        + Tambah User
    </a>
</div>

{{-- ================= DESKTOP TABLE ================= --}}
<div class="hidden md:block">
    <table class="w-full bg-white rounded shadow overflow-hidden">
        <thead class="bg-gray-100 text-sm">
            <tr>
                <th class="p-3 text-left">Nama</th>
                <th class="p-3 text-left">Email</th>
                <th class="p-3 text-left">Role</th>
                <th class="p-3 text-left">Status</th>
                <th class="p-3 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-sm">
            @foreach($users as $user)
            <tr class="border-t hover:bg-gray-50">
                <td class="p-3">{{ $user->name }}</td>
                <td class="p-3">{{ $user->email }}</td>
                <td class="p-3 capitalize">{{ $user->role }}</td>
                <td class="p-3">
                    <span class="px-2 py-1 rounded text-xs
                        {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="p-3">
                    <form method="POST"
                          action="{{ route('users.toggle-status', $user->id) }}"
                          onsubmit="return confirm('Yakin ubah status user ini?')">
                        @csrf
                        @method('PATCH')

                        <button
                            class="px-3 py-1 text-xs rounded text-white
                            {{ $user->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }}">
                            {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- ================= MOBILE CARD ================= --}}
<div class="md:hidden space-y-4">
    @foreach($users as $user)
    <div class="bg-white rounded shadow p-4 space-y-2 text-sm">
        <div>
            <p class="text-gray-500">Nama</p>
            <p class="font-semibold">{{ $user->name }}</p>
        </div>

        <div>
            <p class="text-gray-500">Email</p>
            <p>{{ $user->email }}</p>
        </div>

        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500">Role</p>
                <p class="capitalize">{{ $user->role }}</p>
            </div>

            <span class="px-2 py-1 rounded text-xs
                {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>

        <form method="POST"
              action="{{ route('users.toggle-status', $user->id) }}"
              onsubmit="return confirm('Yakin ubah status user ini?')">
            @csrf
            @method('PATCH')

            <button
                class="w-full py-2 rounded text-white
                {{ $user->is_active ? 'bg-red-600' : 'bg-green-600' }}">
                {{ $user->is_active ? 'Nonaktifkan User' : 'Aktifkan User' }}
            </button>
        </form>
    </div>
    @endforeach
</div>

@endsection
