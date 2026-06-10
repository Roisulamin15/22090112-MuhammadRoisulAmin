@extends('layouts.app')

@section('title', 'Kirim Surat ')

@section('content')

<form method="POST"
      action="{{ isset($surat)
            ? route('surat-keluar.update',$surat->id)
            : route('surat-keluar.store') }}"
      enctype="multipart/form-data"
      class="bg-white p-6 rounded shadow max-w-xl space-y-3">

    @csrf

    @if(isset($surat))
        @method('PUT')
    @endif

    @csrf

    @php
        $prefill = $prefill ?? [];
        $uploaded_file_name = $uploaded_file_name ?? null;
        $ocrFile = old('uploaded_file_name', $uploaded_file_name ?? '');
    @endphp

    {{-- Preview Nomor Surat --}}
        <input type="text"
            id="preview_nomor"
            class="w-full border rounded px-3 py-2 bg-gray-100"
            placeholder="Nomor surat otomatis"
            value="{{ $surat->nomor_surat ?? '' }}"
            readonly>

    {{-- Tujuan Surat --}}
<div class="relative w-full">

    {{-- Tampilan select --}}
    <div id="selectBox"
         class="w-full border rounded px-3 py-2 bg-white cursor-pointer flex justify-between items-center">

        <span id="selectedText">
            -- Pilih Tujuan --
        </span>

        <span>▼</span>

    </div>

    {{-- Dropdown --}}
    <div id="checkboxDropdown"
         class="hidden absolute w-full bg-white border rounded shadow mt-1 z-50 max-h-60 overflow-y-auto">

        {{-- Semua user --}}
        <label class="flex items-center gap-2 px-3 py-2 hover:bg-gray-100">

            <input type="checkbox"
                   id="check_all">

            <span>🔥 Kirim ke Semua User</span>

        </label>

        <hr>

        {{-- User --}}
        @foreach($users as $user)

            <label class="flex items-center gap-2 px-3 py-2 hover:bg-gray-100">

               <input type="checkbox"
                name="tujuan_id[]"
                value="{{ $user->id }}"
                class="user-checkbox"
                data-name="{{ $user->name }}"
                data-phone="{{ $user->phone }}"
                {{ isset($surat) && $surat->tujuan_id == $user->id ? 'checked' : '' }}>

                <span>
                    {{ $user->name }}
                    ({{ $user->role }})
                </span>

            </label>

        @endforeach

    </div>

</div>

{{-- Preview nomor --}}
<input type="text"
       id="preview_phone"
       class="w-full border rounded px-3 py-2 bg-gray-100"
       placeholder="Nomor tujuan otomatis"
       readonly>

    {{-- Tanggal --}}
    <input type="date"
           name="tanggal_surat"
           min="{{ date('Y-m-d') }}"
           class="w-full border rounded px-3 py-2"
           value="{{ old('tanggal_surat',$surat->tanggal_surat?? ($prefill['tanggal_surat'] ?? '')) }}"required>

    <input type="time" name="jam"
       class="w-full border rounded px-3 py-2"
       value="{{ old( 'jam',$surat->jam?? ($prefill['jam'] ?? '')) }}">

    {{-- Perihal --}}
    @php
    $selectedPerihal = strtolower(old('perihal', $surat->perihal ?? ''));
@endphp
<select name="perihal"
        id="perihal"
        class="w-full border rounded px-3 py-2"
        required>

    <option value="">-- Pilih Jenis Surat --</option>

    <option value="surat undangan" {{ $selectedPerihal == 'surat undangan' ? 'selected' : '' }}>
        Surat Undangan
    </option>

    <option value="surat pemberitahuan" {{ $selectedPerihal == 'surat pemberitahuan' ? 'selected' : '' }}>
        Surat Pemberitahuan
    </option>

    <option value="surat permohonan" {{ $selectedPerihal == 'surat permohonan' ? 'selected' : '' }}>
        Surat Permohonan
    </option>

    <option value="surat edaran" {{ $selectedPerihal == 'surat edaran' ? 'selected' : '' }}>
        Surat Edaran
    </option>

    <option value="surat keputusan" {{ $selectedPerihal == 'surat keputusan' ? 'selected' : '' }}>
        Surat Keputusan
    </option>

    <option value="surat keterangan" {{ $selectedPerihal == 'surat keterangan' ? 'selected' : '' }}>
        Surat Keterangan
    </option>

    <option value="surat tugas" {{ $selectedPerihal == 'surat tugas' ? 'selected' : '' }}>
        Surat Tugas
    </option>

    <option value="surat perjalanan dinas" {{ $selectedPerihal == 'surat perjalanan dinas' ? 'selected' : '' }}>
        Surat Perjalanan Dinas
    </option>

    <option value="surat peraturan" {{ $selectedPerihal == 'surat peraturan' ? 'selected' : '' }}>
        Surat Peraturan
    </option>

    <option value="surat pengantar" {{ $selectedPerihal == 'surat pengantar' ? 'selected' : '' }}>
        Surat Pengantar
    </option>

    <option value="surat pernyataan" {{ $selectedPerihal == 'surat pernyataan' ? 'selected' : '' }}>
        Surat Pernyataan
    </option>

    <option value="surat kuasa" {{ $selectedPerihal == 'surat kuasa' ? 'selected' : '' }}>
        Surat Kuasa
    </option>

    <option value="surat peringatan" {{ $selectedPerihal == 'surat peringatan' ? 'selected' : '' }}>
        Surat Peringatan
    </option>

    <option value="surat memo" {{ $selectedPerihal == 'surat memo' ? 'selected' : '' }}>
        Surat Memo
    </option>

    <option value="surat instruksi/perintah" {{ $selectedPerihal == 'surat instruksi/perintah' ? 'selected' : '' }}>
        Surat Instruksi/Perintah
    </option>

    <option value="surat perjanjian" {{ $selectedPerihal == 'surat perjanjian' ? 'selected' : '' }}>
        Surat Perjanjian
    </option>

    <option value="mou" {{ $selectedPerihal == 'mou' ? 'selected' : '' }}>
        MoU
    </option>

    <option value="surat rekomendasi" {{ $selectedPerihal == 'surat rekomendasi' ? 'selected' : '' }}>
        Surat Rekomendasi
    </option>

    <option value="surat balasan" {{ $selectedPerihal == 'surat balasan' ? 'selected' : '' }}>
        Surat Balasan
    </option>

    <option value="surat pengumuman" {{ $selectedPerihal == 'surat pengumuman' ? 'selected' : '' }}>
        Surat Pengumuman
    </option>

    <option value="nota dinas" {{ $selectedPerihal == 'nota dinas' ? 'selected' : '' }}>
        Nota Dinas
    </option>

    <option value="berita acara" {{ $selectedPerihal == 'berita acara' ? 'selected' : '' }}>
        Berita Acara
    </option>

    <option value="piagam-sertifikat" {{ $selectedPerihal == 'piagam-sertifikat' ? 'selected' : '' }}>
        Piagam/Sertifikat
    </option>

    <option value="surat persetujuan" {{ $selectedPerihal == 'surat persetujuan' ? 'selected' : '' }}>
        Surat Persetujuan
    </option>

    <option value="surat kontrak" {{ $selectedPerihal == 'surat kontrak' ? 'selected' : '' }}>
        Surat Kontrak
    </option>

</select>

    {{-- hidden file dari OCR --}}
    <input type="hidden" name="uploaded_file_name" value="{{ $ocrFile }}">

    {{-- preview/link file OCR --}}
    @if($ocrFile)
        <div class="border rounded p-3 bg-gray-50">
            <div class="text-sm text-gray-700 mb-2">
                File hasil OCR sudah tersimpan:
                <a class="underline text-blue-600" target="_blank"
                   href="{{ asset('uploads/surat_keluar/'.$ocrFile) }}">
                    {{ $ocrFile }}
                </a>
            </div>
        </div>
    @endif

    <select name="penanggung_jawab_id"
        class="w-full border rounded px-3 py-2">

    <option value="">
        -- Pilih Penanggung Jawab --
    </option>

    @foreach($penanggungJawab as $pj)

        <option value="{{ $pj->id }}"
    {{ old(
        'penanggung_jawab_id',
        $surat->penanggung_jawab_id ?? ''
    ) == $pj->id ? 'selected' : '' }}>
            {{ $pj->nama }} - {{ $pj->jabatan }}
        </option>

    @endforeach

</select>

    {{-- Upload manual (opsional, override) --}}
    <div class="space-y-1">
        <div class="text-sm text-gray-700">
            {{ $ocrFile ? 'Ganti file (opsional):' : 'Upload file (opsional):' }}
        </div>
        <input type="file" name="file" class="w-full">
    </div>


    <button type="submit"
            class="mt-4 px-4 py-2 bg-[#7A1E1E] text-white rounded hover:bg-[#4B0F0F]">
{{ isset($surat) ? 'Update Surat' : 'Kirim Surat' }}    </button>

</form>

<script>

const nomorSurat = @json($nomorSurat);

const kodeSurat = {
    'surat undangan': '01',
    'surat pemberitahuan': '02',
    'surat permohonan': '03',
    'surat edaran': '04',
    'surat keputusan': '05',
    'surat keterangan': '06',
    'surat tugas': '07',
    'surat perjalanan dinas': '08',
    'surat peraturan': '09',
    'surat pengantar': '10',
    'surat pernyataan': '11',
    'surat kuasa': '12',
    'surat peringatan': '13',
    'surat memo': '14',
    'surat instruksi/perintah': '15',
    'surat perjanjian': '16',
    'MoU': '17',
    'surat rekomendasi': '18',
    'surat balasan': '19',
    'surat pengumuman': '20',
    'nota dinas': '21',
    'berita acara': '22',
    'Piagam-sertifikat': '23',
    'surat persetujuan': '24',
    'surat kontrak': '25',
};

const selectBox =
    document.getElementById('selectBox');

const dropdown =
    document.getElementById('checkboxDropdown');

const selectedText =
    document.getElementById('selectedText');

selectBox.addEventListener('click', () => {

    dropdown.classList.toggle('hidden');

});

const checkAll =
    document.getElementById('check_all');

const userCheckboxes =
    document.querySelectorAll('.user-checkbox');

checkAll.addEventListener('change', function () {

    userCheckboxes.forEach(cb => {

        cb.checked = this.checked;

    });

    updateSelection();

});

userCheckboxes.forEach(cb => {

    cb.addEventListener('change', updateSelection);

});

function updateSelection() {

    let names = [];
    let phones = [];

    userCheckboxes.forEach(cb => {

        if (cb.checked) {

            names.push(
                cb.dataset.name
            );

            if (cb.dataset.phone) {

                phones.push(
                    cb.dataset.phone
                );

            }

        }

    });

    // text select
    if (names.length > 0) {

        selectedText.innerText =
            names.join(', ');

    } else {

        selectedText.innerText =
            '-- Pilih Tujuan --';

    }

    // nomor
    document.getElementById('preview_phone')
        .value = phones.join(', ');

}

document.getElementById('perihal').addEventListener('change', function () {

    let jenis = this.value;

    if (!jenis) return;

    let romawi = [
        'I','II','III','IV','V','VI',
        'VII','VIII','IX','X','XI','XII'
    ];

    let now = new Date();

    let bulan = romawi[now.getMonth()];
    let tahun = now.getFullYear();

    // ambil nomor dari database
    let key = jenis.replaceAll(' ', '_');

    let nomorAwal =
        nomorSurat[key] ?? 1;

    nomorAwal =
        nomorAwal.toString().padStart(3, '0');

    let nomor =
        nomorAwal + '.' +
        kodeSurat[jenis] +
        '/{{ auth()->user()->kode_organisasi }}' +
        '/' +
        bulan +
        '/' +
        tahun;

    document.getElementById('preview_nomor').value = nomor;

});

</script>

@endsection
