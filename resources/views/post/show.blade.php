<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-navy-primary leading-tight">
                Detail Post
            </h2>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ $post->judul }}</h2>
            <p class="text-sm leading-6 text-gray-600">{{ $post->deskripsi }}</p>
        </div>

        <a href="{{ route('posts.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-[#0B4A85]/30 bg-white px-4 py-2 text-sm font-semibold text-[#0B4A85] transition hover:bg-[#0B4A85]/5">
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M17 10a1 1 0 01-1 1H6.414l2.293 2.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 111.414 1.414L6.414 9H16a1 1 0 011 1z" clip-rule="evenodd" />
            </svg>
            Back to Announcements
        </a>
    </div>
</x-app-layout>
