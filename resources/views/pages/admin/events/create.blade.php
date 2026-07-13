@extends('layouts.admin_layouts')

@section('content')

<div class="p-6">

    <div class="flex justify-between mb-6">

        <h1 class="text-2xl font-bold">
            Tambah Event
        </h1>


        <a href="{{ route('admin.events.index') }}"
           class="text-blue-600">

            ← Kembali

        </a>

    </div>



    @if($errors->any())

    <div class="bg-red-100 text-red-700 p-4 rounded mb-4">

        <ul>

            @foreach($errors->all() as $error)

                <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

    @endif





<form action="{{ route('admin.events.store') }}"
      method="POST"
      enctype="multipart/form-data">

@csrf



<div class="bg-white border rounded p-6 space-y-5">


<div class="grid grid-cols-2 gap-5">


<div>

<label class="font-medium">
Judul Event
</label>

<input type="text"
       name="judul"
       value="{{ old('judul') }}"
       class="border w-full rounded p-2">

</div>




<div>

<label class="font-medium">
Kategori
</label>


<select name="kategori_id"
        class="border w-full rounded p-2">


<option value="">
-- pilih kategori --
</option>


@foreach($kategoris as $kategori)

<option value="{{ $kategori->id }}">

{{ $kategori->nama }}

</option>

@endforeach


</select>

</div>





<div>

<label class="font-medium">
Lokasi
</label>

<input type="text"
       name="lokasi"
       class="border w-full rounded p-2">

</div>





<div>

<label class="font-medium">
Tanggal & Waktu
</label>


<input type="datetime-local"
       name="tanggal_waktu"
       class="border w-full rounded p-2">


</div>



</div>




<div>

<label class="font-medium">
Gambar
</label>


<input type="file"
       name="gambar"
       id="gambar"
       class="border w-full rounded p-2">


<img id="preview"
     class="mt-3 w-40 rounded hidden">


</div>





<div>

<label class="font-medium">
Deskripsi
</label>


<textarea name="deskripsi"
          rows="5"
          class="border w-full rounded p-2"></textarea>


</div>




<hr>



<div>

<div class="flex justify-between mb-3">

<h2 class="font-bold text-lg">
Daftar Tiket
</h2>


<button type="button"
        onclick="addTicket()"
        class="bg-green-600 text-white px-3 py-2 rounded">

Tambah Tiket

</button>


</div>



<div id="ticket-container">

</div>



</div>






<button
class="bg-blue-600 text-white px-5 py-2 rounded">

Simpan Event

</button>




</div>



</form>


</div>




<script>


let ticketIndex = 0;



function addTicket(){


let html = `

<div class="border rounded p-4 mb-3">

<div class="flex justify-between">

<h3 class="font-bold">
Tiket #${ticketIndex+1}
</h3>


<button type="button"
onclick="this.parentElement.parentElement.remove()"
class="text-red-600">

Hapus

</button>

</div>



<div class="grid grid-cols-3 gap-3 mt-3">


<select name="tikets[${ticketIndex}][tipe]"
class="border rounded p-2">


<option value="reguler">
Reguler
</option>


<option value="premium">
Premium
</option>


</select>




<input type="number"
name="tikets[${ticketIndex}][harga]"
placeholder="Harga"
class="border rounded p-2">



<input type="number"
name="tikets[${ticketIndex}][stok]"
placeholder="Stok"
class="border rounded p-2">



</div>


</div>

`;



document
.getElementById('ticket-container')
.innerHTML += html;


ticketIndex++;

}



addTicket();





document
.getElementById('gambar')
.addEventListener('change',function(e){


let file=e.target.files[0];


if(file){


let img=document.getElementById('preview');

img.src=URL.createObjectURL(file);

img.classList.remove('hidden');


}


});



</script>


@endsection