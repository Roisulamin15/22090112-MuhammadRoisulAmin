@extends('layouts.app')

@section('title', 'Surat masuk')

@section('content')

@if(session('success'))
  <div class="p-3 mb-4 bg-green-200 text-green-900 rounded">
    {{ session('success') }}
  </div>
@endif

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

{{-- ================= HEADER ACTION ================= --}}
<div class="flex flex-col md:flex-row md:items-center md:justify-end gap-2 mb-6">
    {{-- UPLOAD OCR --}}
    <form id="ocrForm"
          method="POST"
          action="{{ route('surat-masuk.ocr') }}"
          enctype="multipart/form-data">

        @csrf

        <input type="file"
               id="ocrFile"
               name="ocr_file"
               accept=".pdf,.jpg,.jpeg,.png,.xlsx,.xls,.csv"
               class="hidden">

        <button type="button"
                onclick="document.getElementById('ocrFile').click()"
                class="px-4 py-2 bg-[#7A1E1E] text-white rounded hover:bg-[#4B0F0F]">
            Upload Surat
        </button>
    </form>

    {{-- SEARCH --}}
    <form method="GET"
          action="{{ route('surat-masuk.index') }}"
          class="flex items-center gap-2">

        <input type="text"
       name="keyword"
       value="{{ request('keyword') }}"
       placeholder="Cari perihal..."
       class="border rounded px-3 py-2 w-64">

        <input type="date"
       name="tanggal"
       value="{{ request('tanggal') }}"
       class="border rounded px-3 py-2">

        <button type="submit"
                class="px-4 py-2 bg-[#7A1E1E] text-white rounded">
            Cari
        </button>

        @if(request('keyword'))
            <a href="{{ route('surat-masuk.index') }}"
               class="px-4 py-2 bg-gray-300 rounded">
                Reset
            </a>
        @endif
    </form>

    {{-- FILTER --}}
    <form method="GET"
          action="{{ route('surat-masuk.filter') }}">

        <select name="jenis"
                onchange="this.form.submit()"
                class="border rounded px-3 py-2">

            <option value="">Filter Jenis Surat</option>

            <option value="surat undangan">Surat Undangan</option>
            <option value="surat pemberitahuan">Surat Pemberitahuan</option>
            <option value="surat permohonan">Surat Permohonan</option>
            <option value="surat edaran">Surat Edaran</option>
            <option value="surat keputusan">Surat Keputusan</option>
            <option value="surat keterangan">Surat Keterangan</option>
            <option value="surat tugas">Surat Tugas</option>
            <option value="surat perjalanan dinas">Surat Perjalanan Dinas</option>
            <option value="surat peraturan">Surat Peraturan</option>
            <option value="surat pengantar">Surat Pengantar</option>
            <option value="surat pernyataan">Surat Pernyataan</option>
            <option value="surat kuasa">Surat Kuasa</option>
            <option value="surat peringatan">Surat Peringatan</option>
            <option value="surat memo">Surat Memo</option>
            <option value="surat instruksi/perintah">Surat Instruksi/Perintah</option>
            <option value="surat perjanjian">Surat Perjanjian</option>
            <option value="mou">MoU</option>
            <option value="surat rekomendasi">Surat Rekomendasi</option>
            <option value="surat balasan">Surat Balasan</option>
            <option value="surat pengumuman">Surat Pengumuman</option>
            <option value="nota dinas">Nota Dinas</option>
            <option value="berita acara">Berita Acara</option>
            <option value="Piagam-sertifikat">Piagam/Sertifikat</option>
            <option value="surat persetujuan">Surat Persetujuan</option>
            <option value="surat kontrak">Surat Kontrak</option>

        </select>

    </form>

</div>

<script>
document.getElementById('ocrFile').addEventListener('change', function () {
    if (this.files.length > 0) {
        document.getElementById('ocrForm').submit();
    }
});
</script>

<script>
document.getElementById('ocrFile').addEventListener('change', function () {
    if (this.files.length > 0) {
        document.getElementById('ocrForm').submit();
    }
});
</script>


{{-- ================= DESKTOP TABLE ================= --}}
<div class="hidden md:block">
<table class="w-full bg-white rounded shadow">
    <thead class="bg-gray-100">
        <tr>
            <th class="p-3">No</th>
            <th class="p-3">Nomor Surat</th>
            <th class="p-3">asal</th>
            <th class="p-3">Perihal</th>
            <th class="p-3">Penanggung Jawab</th>
            <th class="p-3">Tanggal</th>
            <th class="p-3">Jam</th>
            <th class="p-3">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $row)
        <tr class="border-t">
            <td class="p-3 text-center">{{ $loop->iteration }}</td>
            <td class="p-3">{{ $row->nomor_surat }}</td>
            <td class="p-3">{{ $row->asal_surat }}</td>
            <td class="p-3">{{ $row->perihal }}</td>
            <td class="p-3">
    {{ $row->penanggungJawab->nama ?? '-' }}
</td>
            <td class="p-3">
                {{ \Carbon\Carbon::parse($row->tanggal_surat)->format('d M Y') }}
            </td>

            <td class="p-3">
                {{ $row->jam ? \Carbon\Carbon::parse($row->jam)->format('H:i') : '-' }}
            </td>

            <td class="p-3">
                <div class="flex items-center gap-3">

                {{-- VIEW --}}
                @if($row->file)
                <a href="{{ route('surat-masuk.view', $row->id) }}"
                target="_blank"
                title="Lihat Surat"
                class="hover:scale-110 transition">
                    <img src="{{ asset('image/view.png') }}"
                        alt="View"
                        class="w-5 h-5">
                </a>

                {{-- DOWNLOAD --}}
                <a href="{{ route('surat-masuk.download', $row->id) }}"
                title="Download Surat"
                class="hover:scale-110 transition">
                    <img src="{{ asset('image/download.png') }}"
                        alt="Download"
                        class="w-5 h-5">
                </a>
                @endif

                {{-- HAPUS --}}
                @if((int)$row->penerima_id == (int)auth()->id())

                <form method="POST"
                    action="{{ route('surat-masuk.destroy', $row->id) }}"
                    onsubmit="return confirm('Hapus surat ini?')">

                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            title="Hapus Surat"
                            class="hover:scale-110 transition">
                        <img src="{{ asset('image/delet.png') }}"
                            alt="Delete"
                            class="w-5 h-5">
                    </button>

                </form>

                @endif

            </div>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="p-4 text-center text-gray-500">
                Data surat masuk belum ada
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>

{{-- ================= MOBILE CARD ================= --}}
<div class="md:hidden space-y-4">
@foreach($data as $row)
<div class="bg-white p-4 rounded shadow">
    <p class="font-semibold text-[#7A1E1E]">
        {{ $row->nomor_surat }}
    </p>
    <p class="text-sm">Asal: {{ $row->asal_surat }}</p>
    <p class="text-sm">Perihal: {{ $row->perihal }}</p>
    <p class="text-sm">Tanggal: {{ $row->tanggal_surat }}</p>

    <div class="mt-3 flex gap-2 flex-wrap">
        @if($row->file)
        <a href="{{ route('surat-masuk.view', $row->id) }}"
           class="px-3 py-1 text-sm bg-blue-600 text-white rounded">
            View
        </a>
        <a href="{{ route('surat-masuk.download', $row->id) }}"
           class="px-3 py-1 text-sm bg-green-600 text-white rounded">
            Download
        </a>
        @endif

        @if((int)$row->penerima_id == (int)auth()->id())

            <form method="POST"
                action="{{ route('surat-masuk.destroy', $row->id) }}"
                onsubmit="return confirm('Hapus surat ini?')">

                @csrf
                @method('DELETE')

                <button class="px-3 py-1 text-sm bg-red-600 text-white rounded">
                    Hapus
                </button>
            </form>

            @endif
        
    </div>
</div>
@endforeach
</div>

@endsection
