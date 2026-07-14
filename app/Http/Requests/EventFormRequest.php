<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EventFormRequest extends FormRequest
{
    /**
     * fungsi authorize untuk memeriksa apakah pengguna yang sedang masuk memiliki peran admin. Jika ya,
     * maka pengguna diizinkan untuk membuat atau memperbarui acara.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

            // Event
            'judul' => [
                'required',
                'string',
                'max:255',
            ],

            'deskripsi' => [
                'required',
                'string',
            ],

            'lokasi' => [
                'required',
                'string',
                'max:255',
            ],

            'kategori_id' => [
                'required',
                'exists:kategoris,id',
            ],

            'tanggal_waktu' => [
                'required',
                'date',
                'after:now',
            ],

            'gambar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],

            // Tiket
            'tikets' => [
                'required',
                'array',
                'min:1',
            ],

            'tikets.*.tipe' => [
                'required',
                'in:reguler,premium',
            ],

            'tikets.*.harga' => [
                'required',
                'numeric',
                'min:0',
            ],

            'tikets.*.stok' => [
                'required',
                'integer',
                'min:0',
            ],

            'tikets.*.id' => [
                'nullable',
                'exists:tikets,id',
            ],

        ];
    }

    public function messages(): array
    {
        return [

            'judul.required' => 'Judul event wajib diisi.',

            'deskripsi.required' => 'Deskripsi event wajib diisi.',

            'lokasi.required' => 'Lokasi event wajib diisi.',

            'kategori_id.required' => 'Kategori event wajib dipilih.',

            'kategori_id.exists' => 'Kategori tidak ditemukan.',

            'tanggal_waktu.required' => 'Tanggal event wajib diisi.',

            'tanggal_waktu.after' => 'Tanggal event harus setelah waktu sekarang.',

            'gambar.image' => 'File harus berupa gambar.',

            'gambar.mimes' => 'Format gambar harus jpg, jpeg, atau png.',

            'gambar.max' => 'Ukuran gambar maksimal 2MB.',

            'tikets.required' => 'Minimal harus ada satu tiket.',

            'tikets.min' => 'Minimal harus ada satu tiket.',

            'tikets.*.tipe.required' => 'Tipe tiket wajib dipilih.',

            'tikets.*.harga.required' => 'Harga tiket wajib diisi.',

            'tikets.*.stok.required' => 'Stok tiket wajib diisi.',

        ];
    }
}
