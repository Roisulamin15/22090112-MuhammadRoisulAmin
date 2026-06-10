{{-- resources/views/users/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create User')

@section('content')

<form method="POST"
      action="{{ route('users.store') }}"
      class="bg-white p-6 rounded shadow max-w-xl space-y-3">

    @csrf

    <input type="text" name="name" placeholder="Nama"
           class="w-full border rounded px-3 py-2" required>

    <input type="email" name="email" placeholder="Email"
           class="w-full border rounded px-3 py-2" required>

    <input type="password" name="password" placeholder="Password"
           class="w-full border rounded px-3 py-2" required>

    <select name="role"
            class="w-full border rounded px-3 py-2" required>
        <option value="">-- Pilih Role --</option>
        <option value="admin">Admin</option>
        <option value="user">User</option>
    </select>

    <button class="mt-4 px-4 py-2 bg-[#7A1E1E] text-white rounded">
        Simpan
    </button>
</form>

@endsection
