@extends('layouts.app')

@section('title', 'Reset Password User')

@section('content')

<div class="bg-white rounded-xl shadow p-6 max-w-lg">

    <h2 class="text-lg font-semibold mb-4">
        Reset Password User
    </h2>

    <p class="mb-4 text-gray-600">
        User: <strong>{{ $user->name }}</strong>
    </p>

    <form method="POST"
          action="{{ route('users.password.update', $user->id) }}">

        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block mb-2 font-medium">
                Password Baru
            </label>

            <input type="password"
                   name="password"
                   class="w-full border rounded-lg px-3 py-2"
                   required>
        </div>

        <div class="mb-4">
            <label class="block mb-2 font-medium">
                Konfirmasi Password
            </label>

            <input type="password"
                   name="password_confirmation"
                   class="w-full border rounded-lg px-3 py-2"
                   required>
        </div>

        <button type="submit"
                class="px-4 py-2 bg-[#7A1E1E] text-white rounded-lg">
            Simpan Password Baru
        </button>

    </form>

</div>

@endsection