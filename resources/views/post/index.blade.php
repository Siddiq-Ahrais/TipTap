<x-app-layout>
    @php
        $role = strtolower((string) auth()->user()?->role);
        $isAdminPostManager = in_array($role, ['admin', 'administrator', 'superadmin', 'super admin', 'super_admin'], true);
    @endphp

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-navy-primary leading-tight">
                    {{ __('Announcements') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ $isAdminPostManager
                        ? 'Manage all announcements and posts.'
                        : 'Read the latest announcements and posts from admin.' }}
                </p>
            </div>

            @if ($isAdminPostManager)
                <a href="{{ route('posts.create') }}"
                   class="inline-flex items-center rounded-md px-4 py-2.5 text-sm font-semibold shadow-md transition-all duration-200 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2"
                   style="background-color:#0B4A85;color:#FFFFFF;border:1px solid #0B4A85;">
                    + Tambah Post
                </a>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ showDeleteModal: false, deleteFormId: null, deletePostTitle: '' }">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-4">
            @forelse ($posts as $post)
                <div class="bg-white shadow rounded-xl p-5 border border-[#0B4A85]/15">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">{{ $post->judul }}</h2>
                            <p class="mt-2 text-sm leading-6 text-gray-600">{{ $post->deskripsi }}</p>
                        </div>

                        @if ($isAdminPostManager)
                            <div class="flex items-center gap-2">
                                <a href="{{ route('posts.edit', $post) }}"
                                   class="inline-flex items-center rounded-md border border-yellow-500 px-3 py-2 text-xs font-semibold text-yellow-600 hover:bg-yellow-50 transition">
                                    Edit
                                </a>

                                <form id="delete-post-{{ $post->id }}" action="{{ route('posts.destroy', $post) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <button type="button"
                                        @click="deleteFormId = 'delete-post-{{ $post->id }}'; deletePostTitle = @js($post->judul); showDeleteModal = true"
                                        class="inline-flex items-center rounded-md border border-red-500 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 transition">
                                    Hapus
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white shadow rounded-xl border border-dashed border-[#0B4A85]/30 p-10 text-center">
                    <p class="text-sm text-gray-600">Belum ada data post.</p>
                </div>
            @endforelse
        </div>

        {{-- Delete Confirmation Modal --}}
        <div x-cloak
             x-show="showDeleteModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             @keydown.escape.window="showDeleteModal = false">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showDeleteModal = false"></div>

            {{-- Modal Card --}}
            <div x-show="showDeleteModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                 class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">

                {{-- Navy header stripe --}}
                <div class="bg-[#0B4A85] px-6 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/80">Delete Confirmation</p>
                    <h3 class="mt-1 text-lg font-bold text-white">Are you sure you want to delete?</h3>
                </div>

                {{-- Body --}}
                <div class="px-6 py-5">
                    <p class="text-sm text-slate-600">
                        This action will permanently delete the post
                        <span class="font-semibold text-slate-800" x-text="deletePostTitle"></span>.
                        This cannot be undone.
                    </p>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button type="button"
                                @click="showDeleteModal = false"
                                class="inline-flex items-center justify-center rounded-lg border border-[#0B4A85] bg-white px-5 py-2.5 text-sm font-semibold text-[#0B4A85] hover:bg-[#E7EFF6] transition-all duration-200 min-w-[100px]">
                            No
                        </button>
                        <button type="button"
                                @click="document.getElementById(deleteFormId).submit()"
                                class="inline-flex items-center justify-center rounded-lg bg-[#0B4A85] px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-[#063157] transition-all duration-200 min-w-[100px]">
                            Yes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
