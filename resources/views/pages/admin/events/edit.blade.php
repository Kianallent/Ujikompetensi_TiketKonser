@extends('layouts.admin_layouts')

@section('content')

    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">
                Edit Event
            </h1>
            <a href="{{ route('admin.events.index') }}" class="text-blue-600 hover:underline">
                ← Kembali
            </a>
        </div>

        @if ($hasSales)
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded mb-5">
                <strong>Peringatan!</strong><br>
                Event ini sudah memiliki penjualan tiket.
                Tanggal event tidak dapat diubah.
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded mb-5">
                <ul class="list-disc ml-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="bg-white rounded shadow p-6 space-y-6">
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block mb-2 font-medium">
                            Judul Event
                        </label>
                        <input type="text" name="judul" value="{{ old('judul', $event->judul) }}"
                            class="border rounded w-full p-2">
                    </div>

                    <div>
                        <label class="block mb-2 font-medium">
                            Kategori
                        </label>

                        <select name="kategori_id" class="border rounded w-full p-2">

                            @foreach ($kategoris as $kategori)
                                <option value="{{ $kategori->id }}" @selected(old('kategori_id', $event->kategori_id) == $kategori->id)>

                                    {{ $kategori->nama }}

                                </option>
                            @endforeach

                        </select>

                    </div>

                    <div>

                        <label class="block mb-2 font-medium">
                            Lokasi
                        </label>

                        <input type="text" name="lokasi" value="{{ old('lokasi', $event->lokasi) }}"
                            class="border rounded w-full p-2">

                    </div>

                    <div>

                        <label class="block mb-2 font-medium">
                            Tanggal & Waktu
                        </label>

                        <input type="datetime-local" name="tanggal_waktu"
                            value="{{ old('tanggal_waktu', $event->tanggal_waktu->format('Y-m-d\TH:i')) }}"
                            @if ($hasSales) readonly @endif class="border rounded w-full p-2">

                    </div>

                </div>

                <div>

                    <label class="block mb-2 font-medium">
                        Gambar Baru
                    </label>

                    <input type="file" id="gambar" name="gambar" class="border rounded w-full p-2">

                    <p class="text-sm text-gray-500 mt-2">
                        Kosongkan jika tidak ingin mengganti gambar.
                    </p>

                </div>

                <div>

                    <p class="font-medium mb-2">
                        Gambar Saat Ini
                    </p>

                    <img src="{{ $event->image_url }}" class="w-64 rounded shadow">

                </div>

                <div>

                    <label class="block mb-2 font-medium">
                        Deskripsi
                    </label>

                    <textarea name="deskripsi" rows="5" class="border rounded w-full p-2">{{ old('deskripsi', $event->deskripsi) }}</textarea>

                </div>

                <hr>

                <div class="flex justify-between items-center">

                    <h2 class="text-xl font-bold">
                        Daftar Tiket
                    </h2>

                    @if (!$hasSales)
                        <button type="button" id="addTicket"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">

                            + Tambah Tiket

                        </button>
                    @endif

                </div>

                <div id="ticketContainer">

                    @foreach ($event->tikets as $i => $tiket)
                        <div class="border rounded p-4 mb-4 ticket-card">

                            <input type="hidden" name="tikets[{{ $i }}][id]" value="{{ $tiket->id }}">

                            @if (!$hasSales)
                                <div class="text-right mb-3">

                                    <button type="button"
                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded removeTicket">

                                        Hapus

                                    </button>

                                </div>
                            @endif

                            <div class="grid grid-cols-3 gap-4">

                                <div>

                                    <label class="block mb-2">
                                        Tipe
                                    </label>

                                    <select name="tikets[{{ $i }}][tipe]" class="border rounded w-full p-2">

                                        <option value="reguler" @selected($tiket->tipe == 'reguler')>

                                            Reguler

                                        </option>

                                        <option value="premium" @selected($tiket->tipe == 'premium')>

                                            Premium

                                        </option>

                                    </select>

                                </div>

                                <div>

                                    <label class="block mb-2">
                                        Harga
                                    </label>

                                    <input type="number" name="tikets[{{ $i }}][harga]"
                                        value="{{ $tiket->harga }}" class="border rounded w-full p-2">

                                </div>

                                <div>

                                    <label class="block mb-2">
                                        Stok
                                    </label>

                                    <input type="number" name="tikets[{{ $i }}][stok]"
                                        value="{{ $tiket->stok }}" class="border rounded w-full p-2">

                                </div>

                            </div>

                        </div>
                    @endforeach

                </div>

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">

                    Update Event

                </button>

            </div>

        </form>

    </div>

    @if (!$hasSales)
        <script>
            let ticketIndex = {{ $event->tikets->count() }};

            document.getElementById('addTicket').addEventListener('click', function() {

                const container = document.getElementById('ticketContainer');

                container.insertAdjacentHTML('beforeend', `

<div class="border rounded p-4 mb-4 ticket-card">

<div class="text-right mb-3">

<button
type="button"
class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded removeTicket">

Hapus

</button>

</div>

<div class="grid grid-cols-3 gap-4">

<div>

<label class="block mb-2">

Tipe

</label>

<select
name="tikets[${ticketIndex}][tipe]"
class="border rounded w-full p-2">

<option value="reguler">

Reguler

</option>

<option value="premium">

Premium

</option>

</select>

</div>

<div>

<label class="block mb-2">

Harga

</label>

<input
type="number"
name="tikets[${ticketIndex}][harga]"
class="border rounded w-full p-2"
required>

</div>

<div>

<label class="block mb-2">

Stok

</label>

<input
type="number"
name="tikets[${ticketIndex}][stok]"
class="border rounded w-full p-2"
required>

</div>

</div>

</div>

`);

                ticketIndex++;

            });

            document.addEventListener('click', function(e) {

                if (e.target.classList.contains('removeTicket')) {

                    e.target.closest('.ticket-card').remove();

                }

            });
        </script>
    @endif

@endsection
