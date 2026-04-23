<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-navy-primary leading-tight">
                    {{ __('Data Post') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">Kelola semua postingan dengan tampilan yang rapi.</p>
            </div>

            <a href="{{ route('posts.create') }}"
               class="inline-flex items-center rounded-md px-4 py-2.5 text-sm font-semibold shadow-md transition-all duration-200 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2"
               style="background-color:#0B4A85;color:#FFFFFF;border:1px solid #0B4A85;">
                + Tambah Post
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-4">
            @forelse ($posts as $post)
                <div class="bg-white shadow sm:rounded-lg p-5 border border-[#0B4A85]/15">
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
                <div class="bg-white shadow sm:rounded-lg border border-dashed border-[#0B4A85]/30 p-10 text-center">
                    <p class="text-sm text-gray-600">Belum ada data post.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
