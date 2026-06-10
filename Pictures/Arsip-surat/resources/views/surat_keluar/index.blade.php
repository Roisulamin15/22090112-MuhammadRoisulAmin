@extends('layouts.app')

@section('title', 'Surat Keluar')

@section('content')

<div class="flex flex-col md:flex-row md:items-center md:justify-end gap-3 mb-6">

    {{-- OCR --}}
    <form id="ocrForm"
          method="POST"
          action="{{ route('surat-keluar.ocr') }}"
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
            Upload OCR
        </button>
    </form>

    {{-- SEARCH --}}
    <form method="GET"
          action="{{ route('surat-keluar.index') }}"
          class="flex flex-wrap gap-2">

        <input type="text"
               name="keyword"
               value="{{ request('keyword') }}"
               placeholder="Cari perihal..."
               class="border rounded px-3 py-2 w-64">

        <button type="submit"
                class="px-4 py-2 bg-[#7A1E1E] text-white rounded">
            Cari
        </button>

        @if(request('keyword'))
            <a href="{{ route('surat-keluar.index') }}"
               class="px-4 py-2 bg-gray-300 rounded">
                Reset
            </a>
        @endif
    </form>

    {{-- FILTER TANGGAL --}}
    <form method="GET"
          action="{{ route('surat-keluar.index') }}"
          class="flex gap-2">

        <input type="date"
               name="tanggal"
               value="{{ request('tanggal') }}"
               class="border rounded px-3 py-2">

        <button type="submit"
                class="px-4 py-2 bg-[#7A1E1E] text-white rounded">
            Filter
        </button>

    </form>

    {{-- FILTER JENIS SURAT --}}
    <form method="GET"
          action="{{ route('surat-keluar.filter') }}"
          class="flex gap-2">

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
    

<table class="w-full bg-white rounded shadow">
    <thead class="bg-gray-100">
        <tr>
            <th class="p-3">No</th>
            <th class="p-3">Nomor Surat</th>
            <th class="p-3">Tujuan</th>
            <th class="p-3">Perihal</th>
            <th class="p-3">Penanggung Jawab</th>
            <th class="p-3">Tanggal</th>
            <th class="p-3">Jam</th>
            <th class="p-3">Status</th>
            <th class="p-3">Aksi</th>

        </tr>
    </thead>
    <tbody>
        @forelse($data as $row)
        <tr class="border-t">
            <td class="p-3">{{ $loop->iteration }}</td>
            <td class="p-3">{{ $row->nomor_surat }}</td>
            <td class="p-3">{{ $row->tujuan_surat }}</td>
            <td class="p-3">{{ $row->perihal }}</td>
            <td class="p-3"> {{ $row->penanggungJawab->nama ?? '-' }}</td>
            <td class="p-3">{{ $row->tanggal_surat }}</td>
            <td class="p-3">{{ $row->jam ? \Carbon\Carbon::parse($row->jam)->format('H:i') : '-' }}</td>
            <td class="p-3 text-center">

        @if($row->is_read)

            <img src="{{ asset('image/mata_putih.png') }}"
                alt="Sudah Dibaca"
                class="w-6 h-6 mx-auto">

        @else

            <img src="{{ asset('image/mata_hitam.png') }}"
                alt="Belum Dibaca"
                class="w-6 h-6 mx-auto">

        @endif

    </td>
            
<td class="p-3">
    <div class="flex items-center gap-3">

        {{-- VIEW --}}
@if($row->file)
<a href="{{ route('surat-keluar.view', $row->id) }}"
   target="_blank"
   title="Lihat Surat"
   class="hover:scale-110 transition">
    <img src="{{ asset('image/view.png') }}"
         alt="View"
         class="w-5 h-5">
</a>

{{-- DOWNLOAD --}}
<a href="{{ route('surat-keluar.download', $row->id) }}"
   title="Download Surat"
   class="hover:scale-110 transition">
    <img src="{{ asset('image/download.png') }}"
         alt="Download"
         class="w-5 h-5">
</a>
@endif

{{-- HAPUS --}}
@if((int)$row->pengirim_id == (int)auth()->id())

<form method="POST"
      action="{{ route('surat-keluar.destroy', $row->id) }}"
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

    <a href="{{ route('surat-keluar.edit', $row->id) }}"
   title="Edit Surat"
   class="hover:scale-110 transition">
    <img src="{{ asset('image/edit.png') }}"
         alt="Edit"
         class="w-5 h-5">
</a>


    </div>
</td>


        </tr>
        @empty
        <tr>
            <td colspan="6" class="p-4 text-center text-gray-500">
                Data surat keluar belum ada
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

@endsection
