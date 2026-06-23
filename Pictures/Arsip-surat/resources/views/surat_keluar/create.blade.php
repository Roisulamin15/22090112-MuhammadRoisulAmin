@extends('layouts.app')

@section('title', 'Kirim Surat ')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

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

<input type="text"
       name="perihal"
       id="perihal"
       list="jenisSuratList"
       class="w-full border rounded px-3 py-2"
       placeholder="Ketik jenis surat..."
       value="{{ old('perihal', $surat->perihal ?? '') }}"
       required>

<datalist id="jenisSuratList">
    <option value="surat undangan">
    <option value="surat pemberitahuan">
    <option value="surat permohonan">
    <option value="surat edaran">
    <option value="surat keputusan">
    <option value="surat keterangan">
    <option value="surat tugas">
    <option value="surat perjalanan dinas">
    <option value="surat peraturan">
    <option value="surat pengantar">
    <option value="surat pernyataan">
    <option value="surat kuasa">
    <option value="surat peringatan">
    <option value="surat memo">
    <option value="surat instruksi/perintah">
    <option value="surat perjanjian">
    <option value="mou">
    <option value="surat rekomendasi">
    <option value="surat balasan">
    <option value="surat pengumuman">
    <option value="nota dinas">
    <option value="berita acara">
    <option value="piagam-sertifikat">
    <option value="surat persetujuan">
    <option value="surat kontrak">
</datalist>

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

    <input type="text"
       id="penanggung_jawab_search"
       list="penanggungJawabList"
       class="w-full border rounded px-3 py-2"
       placeholder="Ketik nama penanggung jawab"
       value="{{ isset($surat) && $surat->penanggungJawab
            ? $surat->penanggungJawab->nama.' - '.$surat->penanggungJawab->jabatan
            : '' }}">
            
    <p id="pjError" class="text-red-500 text-sm mt-1 hidden">
    Penanggung jawab tidak ditemukan.
    </p>

    @error('penanggung_jawab_id')
    <p class="text-red-500 text-sm mt-1">
        {{ $message }}
    </p>
@enderror

    <input type="hidden"
        name="penanggung_jawab_id"
        id="penanggung_jawab_id"
        value="{{ old('penanggung_jawab_id', $surat->penanggung_jawab_id ?? '') }}">

    <datalist id="penanggungJawabList">
        @foreach($penanggungJawab as $pj)
            <option
                data-id="{{ $pj->id }}"
                value="{{ $pj->nama }} - {{ $pj->jabatan }}">
            </option>
        @endforeach
    </datalist>

    {{-- Upload manual (opsional, override) --}}
    <div class="space-y-1">
        <div class="text-sm text-gray-700">
            {{ $ocrFile ? 'Ganti file (opsional):' : 'Upload file (opsional):' }}
        </div>
        <input type="file"name="file"id="fileInput"class="w-full">
    </div>


    <button type="submit"
            class="mt-4 px-4 py-2 bg-[#7A1E1E] text-white rounded hover:bg-[#4B0F0F]">
    {{ isset($surat) ? 'Update Surat' : 'Kirim Surat' }}</button>

    </form>

{{-- PREVIEW --}}
<div class="bg-white p-4 rounded shadow">

    <h3 class="font-semibold mb-3">
        Preview Dokumen
    </h3>

    <div id="previewContainer"
         class="border rounded bg-gray-50 min-h-[500px] flex items-center justify-center">

        @if($ocrFile)

            @php
                $ext = strtolower(pathinfo($ocrFile, PATHINFO_EXTENSION));
            @endphp

            @if(in_array($ext,['jpg','jpeg','png']))
                <img src="{{ asset('uploads/surat_keluar/'.$ocrFile) }}"
                     class="max-h-[700px] max-w-full object-contain">
            @elseif($ext == 'pdf')
                <iframe
                    src="{{ asset('uploads/surat_keluar/'.$ocrFile) }}"
                    class="w-full h-[700px]">
                </iframe>
            @else
                <div class="text-gray-500">
                    {{ $ocrFile }}
                </div>
            @endif

        @else

            <span class="text-gray-400">
                Belum ada file dipilih
            </span>

        @endif

    </div>

</div>

</div>

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

document.getElementById('perihal').addEventListener('input', function () {

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

    const fileInput =
    document.getElementById('fileInput');

const previewContainer =
    document.getElementById('previewContainer');

if(fileInput){

    fileInput.addEventListener('change', function(){

        const file = this.files[0];

        if(!file) return;

        const url =
            URL.createObjectURL(file);

        if(file.type.startsWith('image/')){

            previewContainer.innerHTML = `
                <img src="${url}"
                     class="max-h-[700px] max-w-full object-contain">
            `;

        }
        else if(file.type === 'application/pdf'){

            previewContainer.innerHTML = `
                <iframe
                    src="${url}"
                    class="w-full h-[700px]">
                </iframe>
            `;

        }
        else{

            previewContainer.innerHTML = `
                <div class="text-center">
                    <div class="font-semibold">
                        ${file.name}
                    </div>
                    <div class="text-gray-500">
                        Preview tidak tersedia
                    </div>
                </div>
            `;

        }

    });

}

const pjSearch = document.getElementById('penanggung_jawab_search');
const pjId = document.getElementById('penanggung_jawab_id');
const pjError = document.getElementById('pjError');

pjSearch.addEventListener('input', function () {

    const options =
        document.querySelectorAll('#penanggungJawabList option');

    let ditemukan = false;

    pjId.value = '';

    options.forEach(option => {

        if (option.value === this.value) {

            pjId.value = option.dataset.id;
            ditemukan = true;

        }

    });

    if (this.value !== '' && !ditemukan) {

        pjError.classList.remove('hidden');

    } else {

        pjError.classList.add('hidden');

    }

});

</script>

@endsection
