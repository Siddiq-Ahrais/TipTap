<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
            <h2 class="font-semibold text-xl text-navy-primary leading-tight">
                {{ __('Leaves') }}
            </h2>
            <a href="{{ route('leaves.create') }}" class="mt-4 sm:mt-0 inline-flex items-center justify-center gap-2 rounded-md px-4 py-2 text-sm font-semibold shadow-md transition-all duration-200 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2" style="background-color:#0B4A85;color:#FFFFFF;border:1px solid #0B4A85;">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Request Leave
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
            @if (session('status'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('attendance'))
                <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
                    {{ $errors->first('attendance') }}
                </div>
            @endif



            <!-- Filter & Sorting -->
            <div class="bg-white shadow sm:rounded-lg mb-6 p-4 border border-[#0B4A85]/15">
                <form action="{{ route('leaves.index') }}" method="GET" class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center space-x-2">
                        <label for="status" class="text-sm font-medium text-dark-slate">Filter by Status:</label>
                        <select id="status" name="status" onchange="this.form.submit()" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-[#0B4A85]/25 focus:outline-none focus:ring-[#0B4A85] focus:border-[#0B4A85] sm:text-sm rounded-md transition-colors">
                            <option value="">All Statuses</option>
                            <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Approved</option>
                            <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                </form>
            </div>

            <!-- List/Table View -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-[#0B4A85]/15">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-[#0B4A85]">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                    Submission Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                    Leave Type
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                    Duration
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Details</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($history as $leave)
                                @php
                                    $start = \Carbon\Carbon::parse($leave->tanggal_mulai);
                                    $end = \Carbon\Carbon::parse($leave->tanggal_selesai);
                                    $days = $start->diffInDays($end) + 1; // inclusive of start day
                                @endphp
                                <tr class="toggle-row cursor-pointer transition-all duration-200 hover:bg-navy-light/70 hover:opacity-95" data-target="details-{{ $leave->id }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $leave->created_at->format('M d, Y') }}
                                        <div class="text-xs text-gray-500">{{ $leave->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $leave->jenis_izin }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $start->format('M d') }} - {{ $end->format('M d, Y') }}
                                        <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-[#E7EFF6] text-[#063157] border border-[#0B4A85]/20">
                                            {{ $days }} {{ Str::plural('day', $days) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if (strtolower($leave->status_approval) === 'approved')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#E7EFF6] text-[#063157] border border-[#0B4A85]/30">
                                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-[#0B4A85]" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                                                Approved
                                            </span>
                                        @elseif (strtolower($leave->status_approval) === 'rejected')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-300">
                                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-rose-600" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                                                Rejected
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#E7EFF6] text-[#0B4A85] border border-[#0B4A85]/30">
                                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-[#0B4A85] animate-pulse" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button class="text-navy-primary hover:text-[#063157] transition-colors">
                                            View Details
                                            <svg class="w-4 h-4 inline transform transition-transform duration-200 arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Expandable Details Row -->
                                <tr id="details-{{ $leave->id }}" class="hidden bg-navy-light/40 border-b border-[#0B4A85]/10 w-full transition-all duration-300 ease-in-out">
                                    <td colspan="5" class="px-6 py-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <h4 class="text-sm font-bold text-gray-900 mb-2">Reason for Absence</h4>
                                                <p class="text-sm text-gray-700 p-3 bg-white rounded-md border border-[#0B4A85]/15 shadow-sm whitespace-pre-wrap">{{ $leave->alasan }}</p>
                                                
                                                @if(strtolower($leave->status_approval) === 'rejected')
                                                    <h4 class="text-sm font-bold text-rose-700 mt-4 mb-2">Rejection Reason</h4>
                                                    <p class="text-sm text-rose-700 p-3 bg-rose-50 rounded-md border border-rose-300 shadow-sm whitespace-pre-wrap">{{ $leave->rejection_reason ?? 'No detailed reason provided.' }}</p>
                                                @endif
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-bold text-gray-900 mb-2">Supporting Document</h4>
                                                @if ($leave->bukti_file)
                                                    <div class="flex items-center space-x-3 p-3 bg-white rounded-md border border-[#0B4A85]/15 shadow-sm">
                                                        <div class="flex-shrink-0">
                                                            @php
                                                                $extension = pathinfo($leave->bukti_file, PATHINFO_EXTENSION);
                                                            @endphp
                                                            @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                                                                <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                            @else
                                                                <svg class="h-8 w-8 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>
                                                            @endif
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                                {{ basename($leave->bukti_file) }}
                                                            </p>
                                                        </div>
                                                        <div>
                                                            <a href="{{ Storage::url($leave->bukti_file) }}" target="_blank" class="inline-flex items-center px-2.5 py-1.5 border border-navy-primary/30 shadow-sm text-xs font-medium rounded text-navy-primary bg-white hover:bg-navy-primary/5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0B4A85]">
                                                                View
                                                            </a>
                                                        </div>
                                                    </div>
                                                @else
                                                    <p class="text-sm text-gray-500 italic mt-2">No attachments provided.</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        <p class="text-lg font-medium">No leave records found</p>
                                        <p class="text-sm">You haven't submitted any leave requests yet.</p>
                                        <a href="{{ route('leaves.create') }}" class="mt-4 inline-block text-navy-primary hover:text-[#063157] font-medium">Submit your first request &rarr;</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($history->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $history->links() }}
                </div>
                @endif
            </div>
    </div>

    <!-- Script for Expandable Rows -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleRows = document.querySelectorAll('.toggle-row');
            
            toggleRows.forEach(row => {
                row.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const targetElement = document.getElementById(targetId);
                    const arrowIcon = this.querySelector('.arrow-icon');
                    
                    if (targetElement.classList.contains('hidden')) {
                        // Close all other open rows first for a cleaner look (accordion style)
                        document.querySelectorAll('[id^="details-"]').forEach(el => {
                            if (!el.classList.contains('hidden') && el.id !== targetId) {
                                el.classList.add('hidden');
                                // Reset other arrows
                                const otherId = el.id.replace('details-', '');
                                const otherRow = document.querySelector(`[data-target="${el.id}"]`);
                                if(otherRow) {
                                    const otherArrow = otherRow.querySelector('.arrow-icon');
                                    if(otherArrow) otherArrow.classList.remove('rotate-180');
                                }
                            }
                        });

                        targetElement.classList.remove('hidden');
                        if(arrowIcon) arrowIcon.classList.add('rotate-180');
                    } else {
                        targetElement.classList.add('hidden');
                        if(arrowIcon) arrowIcon.classList.remove('rotate-180');
                    }
                });
            });
        });
    </script>
</x-app-layout>