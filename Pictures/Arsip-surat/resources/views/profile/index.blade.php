@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')

<div class="max-w-2xl bg-white rounded shadow p-6">

    {{-- HEADER --}}
    <div class="flex items-center gap-4 mb-6">

        <div class="w-16 h-16 rounded-full bg-[#7A1E1E]
                    flex items-center justify-center
                    text-white text-2xl font-bold">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>

        <div>
            <h2 class="text-lg font-semibold">
                {{ $user->name }}
            </h2>

            <p class="text-sm text-gray-500">
                {{ $user->email }}
            </p>
        </div>

    </div>

    <hr class="mb-4">

    {{-- INFO --}}
    <div class="space-y-3 text-sm">

        <div class="flex justify-between">
            <span class="text-gray-500">Role</span>

            <span class="font-medium capitalize">
                {{ $user->role }}
            </span>
        </div>

        <div class="flex justify-between">
            <span class="text-gray-500">Nomor Telepon</span>

            <span class="font-medium">
                {{ $user->phone ?? '-' }}
            </span>
        </div>

        <div class="flex justify-between">
            <span class="text-gray-500">Bergabung</span>

            <span class="font-medium">
                {{ $user->created_at->format('d M Y') }}
            </span>
        </div>

    </div>

    {{-- ACTION BUTTON --}}
    <div class="mt-6 flex gap-3">

        <a href="{{ route('profile.edit') }}"
           class="flex-1 text-center px-4 py-2
                  bg-[#7A1E1E] text-white rounded
                  hover:bg-[#4B0F0F] transition">
            Edit Profil
        </a>

        <a href="{{ route('profile.password') }}"
           class="flex-1 text-center px-4 py-2
                  bg-[#7A1E1E] text-white rounded
                  hover:bg-[#4B0F0F] transition">
            Edit Password
        </a>

        <a href="{{ route('nomor-surat.index') }}"
            class="flex-1 text-center px-4 py-2
                   bg-[#7A1E1E] text-white rounded
                  hover:bg-[#4B0F0F] transition">
                Input Nomor Surat
            </a>

            <a href="{{ route('penanggung-jawab.index') }}" 
            class="text-center px-4 py-2
             bg-[#7A1E1E] text-white rounded hover:bg-[#4B0F0F] transition">
              Penanggung Jawab
             </a>

    </div>

</div>

@endsection