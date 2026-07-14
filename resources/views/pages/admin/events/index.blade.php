@extends('layouts.admin_layouts')

@section('content')

<div class="p-6">

    <div class="flex justify-between items-center mb-6">

        <h1 class="text-2xl font-bold">
            Manajemen Event
        </h1>


        <a href="{{ route('admin.events.create') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded">

            Tambah Event

        </a>

    </div>



    {{-- Alert --}}

    @if(session('success'))

    <div class="bg-green-100 text-green-700 p-3 rounded mb-4">

        {{ session('success') }}

    </div>

    @endif



    @if($errors->any())

    <div class="bg-red-100 text-red-700 p-3 rounded mb-4">

        {{ $errors->first() }}

    </div>

    @endif




    {{-- Filter --}}

    <form method="GET"
        class="grid grid-cols-4 gap-4 mb-6">


        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Cari judul/lokasi"
            class="border rounded px-3 py-2">



        <select name="kategori_id"
            class="border rounded px-3 py-2">


            <option value="">
                Semua Kategori
            </option>


            @foreach($kategoris as $kategori)

            <option value="{{ $kategori->id }}"
                @selected(request('kategori_id')==$kategori->id)>

                {{ $kategori->nama }}

            </option>

            @endforeach


        </select>



        <select name="sort"
            class="border rounded px-3 py-2">

            <option value="asc">
                Terlama
            </option>


            <option value="desc"
                @selected(request('sort')=='desc' )>

                Terbaru

            </option>


        </select>



        <button
            class="bg-gray-800 text-white rounded">

            Filter

        </button>


    </form>





    {{-- Table --}}

    <div class="overflow-x-auto">


        <table class="w-full border">

            <thead class="bg-gray-100">

                <tr>

                    <th class="p-3">
                        Gambar
                    </th>


                    <th class="p-3">
                        Judul
                    </th>


                    <th class="p-3">
                        Kategori
                    </th>


                    <th class="p-3">
                        Tanggal
                    </th>


                    <th class="p-3">
                        Lokasi
                    </th>


                    <th class="p-3">
                        Status
                    </th>


                    <th class="p-3">
                        Action
                    </th>


                </tr>

            </thead>



            <tbody>


                @forelse($events as $event)


                <tr class="border-t">


                    <td class="p-3">

                        <img src="{{ $event->image_url }}"
                            class="w-16 h-16 object-cover rounded">

                    </td>



                    <td class="p-3">

                        {{ $event->judul }}

                    </td>



                    <td class="p-3">

                        {{ $event->kategori->nama }}

                    </td>



                    <td class="p-3">

                        {{ $event->tanggal_waktu->format('d M Y H:i') }}

                    </td>



                    <td class="p-3">

                        {{ $event->lokasi }}

                    </td>




                    <td class="p-3">


                        @if($event->status=="Upcoming")

                        <span class="bg-blue-100 px-2 py-1 rounded">
                            Upcoming
                        </span>


                        @elseif($event->status=="Ongoing")

                        <span class="bg-green-100 px-2 py-1 rounded">
                            Ongoing
                        </span>


                        @else

                        <span class="bg-gray-200 px-2 py-1 rounded">
                            Completed
                        </span>

                        @endif


                    </td>



                    <td class="p-3">


                        <a href="{{ route('events.show',$event) }}"
                            class="text-blue-600">

                            View

                        </a>



                        <a href="{{ route('admin.events.edit',$event) }}"
                            class="text-yellow-600 ml-3">

                            Edit

                        </a>




                        <form action="{{ route('admin.events.destroy',$event) }}"
                            method="POST"
                            class="inline">


                            @csrf

                            @method('DELETE')


                            <button
                                onclick="return confirm('Hapus event?')"
                                class="text-red-600 ml-3">

                                Delete

                            </button>


                        </form>


                    </td>


                </tr>


                @empty


                <tr>

                    <td colspan="7"
                        class="text-center p-5">

                        Belum ada event

                    </td>

                </tr>


                @endforelse


            </tbody>


        </table>


    </div>



    <div class="mt-5">

        {{ $events->appends(request()->except('page'))->links() }}

    </div>



</div>


@endsection