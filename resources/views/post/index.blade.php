@extends('layouts.app')
@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Data Post</h1>
            <p class="mt-1 text-sm text-gray-600">Kelola semua postingan dengan tampilan yang rapi.</p>
        </div>
        <a href="{{ route('posts.create') }}"
           class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition">
            + Tambah Post
        </a>
    </div>


    @if (session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif


    <div class="space-y-4">
        @forelse ($posts as $post)
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ $post->judul }}</h2>
                        <p class="mt-2 text-sm leading-6 text-gray-600">{{ $post->deskripsi }}</p>
                    </div>


                    <div class="flex items-center gap-2">
                        <a href="{{ route('posts.edit', $post) }}"
                           class="inline-flex items-center rounded-md border border-yellow-500 px-3 py-2 text-xs font-semibold text-yellow-600 hover:bg-yellow-50 transition">
                            Edit
                        </a>


                        <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus post ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center rounded-md border border-red-500 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center">
                <p class="text-sm text-gray-600">Belum ada data post.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
