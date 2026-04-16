@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Post</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Perbarui data post yang dipilih.</p>
        </div>


        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-semibold mb-1">Terjadi kesalahan:</p>
                <ul class="list-disc ps-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <form action="{{ route('posts.update', $post) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')


            <div>
                <label for="judul" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Judul Post</label>
                <input
                    id="judul"
                    type="text"
                    name="judul"
                    value="{{ old('judul', $post->judul) }}"
                    placeholder="Masukkan judul post"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                >
                @error('judul')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>


            <div>
                <label for="deskripsi" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">Deskripsi</label>
                <textarea
                    id="deskripsi"
                    name="deskripsi"
                    rows="6"
                    placeholder="Masukkan isi post"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                >{{ old('deskripsi', $post->deskripsi) }}</textarea>
                @error('deskripsi')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>


            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="inline-flex items-center rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition">
                    Update
                </button>
                <a href="{{ route('posts.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-100 transition dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
