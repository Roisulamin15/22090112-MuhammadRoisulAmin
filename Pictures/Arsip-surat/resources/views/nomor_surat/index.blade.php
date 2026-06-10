@extends('layouts.app')

@section('title', 'Input Nomor Surat')

@section('content')

<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">

    <h2 class="text-xl font-bold mb-6">
        Input Nomor Surat
    </h2>

    <form action="{{ route('nomor-surat.store') }}"
          method="POST"
          class="space-y-4">

        @csrf

        @foreach($jenisSurat as $key => $label)

    <div>
        <label class="block text-sm font-medium mb-1 capitalize">
            {{ $label }}
        </label>

        <input type="text"
               name="{{ $key }}"
               value="{{ $nomorSurats[$key]->nomor ?? '' }}"
               class="w-full border rounded px-3 py-2">
    </div>

@endforeach

        <button class="bg-[#7A1E1E] text-white px-4 py-2 rounded">
            Simpan
        </button>

    </form>

</div>

@endsection