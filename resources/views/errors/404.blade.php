@extends('errors.layout')

@section('title', 'Halaman Tidak Ditemukan')
@section('code', '404')

@section('icon')
<svg class="w-12 h-12 text-purple-600 dark:text-purple-500 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
</svg>
@endsection

@section('message')
    Halaman yang Anda cari tidak dapat ditemukan. Kemungkinan tautan rusak, halaman telah dihapus, atau terjadi kesalahan pengetikan alamat web.
@endsection
