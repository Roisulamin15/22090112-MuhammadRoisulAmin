@extends('layouts.app')

@section('content')

<h1 class="text-2xl font-bold mb-4">
    Penanggung Jawab
</h1>

@if(session('success'))
<div class="bg-green-200 text-green-800 p-2 rounded mb-3">
    {{ session('success') }}
</div>
@endif

<form action="{{ route('penanggung-jawab.store') }}" method="POST" class="mb-5">
    @csrf

    <div class="mb-3">
        <label class="block mb-1">Nama</label>
        <input type="text"
               name="nama"
               class="border rounded w-full p-2"
               required>
    </div>

    <div class="mb-3">
        <label class="block mb-1">Jabatan</label>
        <input type="text"
               name="jabatan"
               class="border rounded w-full p-2"
               required>
    </div>

    <button type="submit"
            class="bg-[#7A1E1E] text-white rounded
                  hover:bg-[#4B0F0F] transition">
        Simpan
    </button>
</form>

@foreach($data as $item)

<div class="border p-3 rounded mb-2 flex justify-between items-center">

    <div>
        <div>
            <strong>Nama:</strong>
            {{ $item->nama }}
        </div>

        <div>
            <strong>Jabatan:</strong>
            {{ $item->jabatan }}
        </div>
    </div>

    <form method="POST"
          action="{{ route('penanggung-jawab.destroy', $item->id) }}"
          onsubmit="return confirm('Yakin ingin menghapus data ini?')">

        @csrf
        @method('DELETE')

        <button type="submit"
                class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-800">
            Hapus
        </button>

    </form>

</div>

@endforeach

@endsection