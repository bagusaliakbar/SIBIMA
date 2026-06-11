@extends('errors.layout')

@section('title', 'Sesi Halaman Berakhir')
@section('code', '419')

@section('icon')
<svg class="w-12 h-12 text-amber-600 dark:text-amber-500 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
</svg>
@endsection

@section('message')
    Halaman ini telah kedaluwarsa karena ketidakaktifan yang cukup lama. Silakan segarkan halaman dan masuk kembali untuk melanjutkan aktivitas Anda.
@endsection
