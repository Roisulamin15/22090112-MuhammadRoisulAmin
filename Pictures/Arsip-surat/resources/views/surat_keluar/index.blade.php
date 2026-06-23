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
            Cari
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
    

<div class="bg-white rounded-xl shadow-sm overflow-hidden">

    <div class="overflow-x-auto">

        <table class="w-full text-sm">

            <thead class="bg-gray-50 border-b">
                <tr>

                    <th class="px-4 py-3 text-center font-semibold text-gray-700">
                        No
                    </th>

                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                        Nomor Surat
                    </th>

                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                        Tujuan
                    </th>

                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                        Perihal
                    </th>

                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                        Penanggung Jawab
                    </th>

                    <th class="px-4 py-3 text-center font-semibold text-gray-700">
                        Tanggal
                    </th>

                    <th class="px-4 py-3 text-center font-semibold text-gray-700">
                        Jam
                    </th>

                    <th class="px-4 py-3 text-center font-semibold text-gray-700">
                        Status
                    </th>

                    <th class="px-4 py-3 text-center font-semibold text-gray-700">
                        Aksi
                    </th>

                </tr>
            </thead>

           <tbody>

@forelse($data as $row)

<tr class="border-t hover:bg-gray-50 transition">

    <td class="p-3 text-center">
        {{ $loop->iteration }}
    </td>

    <td class="p-3">
        <div class="max-w-[320px] break-words text-sm">
            {{ $row->nomor_surat }}
        </div>
    </td>

    <td class="p-3">
        {{ $row->tujuan_surat }}
    </td>

    <td class="p-3">
        {{ $row->perihal }}
    </td>

    <td class="p-3">
        {{ $row->penanggungJawab->nama ?? '-' }}
    </td>

    <td class="p-3 text-center">
        {{ \Carbon\Carbon::parse($row->tanggal_surat)->format('d M Y') }}
    </td>

    <td class="p-3 text-center">
        {{ $row->jam ? \Carbon\Carbon::parse($row->jam)->format('H:i') : '-' }}
    </td>

    <td class="p-3 text-center">

        @if($row->is_read)
            <img src="{{ asset('image/mata_putih.png') }}"
                 class="w-5 h-5 mx-auto">
        @else
            <img src="{{ asset('image/mata_hitam.png') }}"
                 class="w-5 h-5 mx-auto">
        @endif

    </td>

    <td class="p-3">

        <div class="flex justify-center items-center gap-2">

            @if($row->file)

            <a href="{{ route('surat-keluar.view', $row->id) }}"
               target="_blank"
               title="Lihat Surat"
               class="w-8 h-8 rounded-lg bg-blue-100 hover:bg-blue-200 flex items-center justify-center transition">

                <img src="{{ asset('image/view.png') }}"
                     class="w-4 h-4">

            </a>

            <a href="{{ route('surat-keluar.download', $row->id) }}"
               title="Download Surat"
               class="w-8 h-8 rounded-lg bg-green-100 hover:bg-green-200 flex items-center justify-center transition">

                <img src="{{ asset('image/download.png') }}"
                     class="w-4 h-4">

            </a>

            @endif

            <a href="{{ route('surat-keluar.edit', $row->id) }}"
               title="Edit Surat"
               class="w-8 h-8 rounded-lg bg-yellow-100 hover:bg-yellow-200 flex items-center justify-center transition">

                <img src="{{ asset('image/edit.png') }}"
                     class="w-4 h-4">

            </a>

            @if((int)$row->pengirim_id == (int)auth()->id())

            <form method="POST"
                  action="{{ route('surat-keluar.destroy', $row->id) }}"
                  onsubmit="return confirm('Hapus surat ini?')">

                @csrf
                @method('DELETE')

                <button type="submit"
                        title="Hapus Surat"
                        class="w-8 h-8 rounded-lg bg-red-100 hover:bg-red-200 flex items-center justify-center transition">

                    <img src="{{ asset('image/delet.png') }}"
                         class="w-4 h-4">

                </button>

            </form>

            @endif

        </div>

    </td>

</tr>

@empty

<tr>
    <td colspan="9"
        class="p-8 text-center text-gray-500">
        Data surat keluar belum ada
    </td>
</tr>

@endforelse

</tbody>

        </table>

    </div>

</div>

@endsection
