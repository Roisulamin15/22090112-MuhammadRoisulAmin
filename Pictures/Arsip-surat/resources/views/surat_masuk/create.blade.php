@extends('layouts.app')

@section('title', 'Tambah Surat masuk')

@section('content')

@if(session('error'))
  <div class="p-3 mb-4 bg-red-200 text-red-900 rounded">
    {{ session('error') }}
  </div>
@endif

@if($errors->any())
  <div class="p-3 mb-4 bg-red-200 text-red-900 rounded">
    {{ $errors->first() }}
  </div>
@endif

<form method="POST"
      action="{{ route('surat-masuk.store') }}"
      enctype="multipart/form-data"
      class="bg-white p-6 rounded shadow max-w-xl space-y-3">
    @csrf

    @php
        $prefill = $prefill ?? [];
        $uploaded_file_name = $uploaded_file_name ?? null;
        $ocrFile = old('uploaded_file_name', $uploaded_file_name ?? '');
    @endphp

    {{-- Nomor Surat --}}
    <input type="text" name="nomor_surat" placeholder="Nomor Surat"
           class="w-full border rounded px-3 py-2"
           value="{{ old('nomor_surat', $prefill['nomor_surat'] ?? '') }}"
           required>

    {{-- Tujuan Surat --}}
    <input type="text" name="asal_surat" placeholder="asal Surat"
           class="w-full border rounded px-3 py-2"
           value="{{ old('asal_surat', $prefill['asal_surat'] ?? '') }}"
           required>

    {{-- Tanggal Surat --}}
    <input type="date" name="tanggal_surat"
           class="w-full border rounded px-3 py-2"
           value="{{ old('tanggal_surat', $prefill['tanggal_surat'] ?? '') }}"
           required>

    {{-- Jam Surat --}}
    <input type="time" name="jam"
          class="w-full border rounded px-3 py-2"
          value="{{ old('jam', $prefill['jam'] ?? '') }}">

    {{-- Perihal --}}
    <input type="text" name="perihal" placeholder="Perihal"
           class="w-full border rounded px-3 py-2"
           value="{{ old('perihal', $prefill['perihal'] ?? '') }}"
           required>

    <!-- {{-- Penanggung jawab --}}
    <input type="text" name="penanggung_jawab" placeholder="Penanggung_jawab"
           class="w-full border rounded px-3 py-2"
           value="{{ old('Penanggung_jawab', $prefill['Penanggung_jawab'] ?? '') }}"
           required> -->

    {{-- Hidden file dari OCR --}}
    <input type="hidden" name="uploaded_file_name" value="{{ $ocrFile }}">

    {{-- Preview file OCR (kalau ada) --}}
    @if($ocrFile)
        <div class="border rounded p-3 bg-gray-50">
            <div class="text-sm text-gray-700">
                File hasil OCR sudah tersimpan:
                <a class="underline text-blue-600" target="_blank"
                   href="{{ asset('uploads/surat_masuk/'.$ocrFile) }}">
                    {{ $ocrFile }}
                </a>
            </div>
        </div>
    @endif

    {{-- Upload manual (optional override) --}}
    <div class="space-y-1">
        <div class="text-sm text-gray-700">
            {{ $ocrFile ? 'Ganti file (opsional):' : 'Upload file (opsional):' }}
        </div>
        <input type="file" name="file" class="w-full">
    </div>

       <button type="submit"
              formaction="{{ route('surat-masuk.store') }}"
              formmethod="POST"
              class="mt-4 px-4 py-2 bg-[#7A1E1E] text-white rounded hover:bg-[#4B0F0F]">
       Simpan
       </button>

</form>

@endsection
