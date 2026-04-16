@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto mt-10 text-white">
    <h1 class="text-2xl font-bold mb-4">Detail Post</h1>


    <div class="bg-gray-800 p-6 rounded">
        <h2 class="text-xl font-semibold mb-2">{{ $post->judul }}</h2>
        <p class="text-gray-200">{{ $post->deskripsi }}</p>
    </div>


    <a href="{{ route('posts.index') }}" class="inline-block mt-4 bg-gray-600 px-4 py-2 rounded">
        Kembali
    </a>
</div>
@endsection
