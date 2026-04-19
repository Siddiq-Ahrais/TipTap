<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Leave History') }}
            </h2>
            <a href="{{ route('leaves.create') }}" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                New Request
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter & Sorting -->
            <div class="bg-white shadow sm:rounded-lg mb-6 p-4">
                <form action="{{ route('leaves.index') }}" method="GET" class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center space-x-2">
                        <label for="status" class="text-sm font-medium text-gray-700">Filter by Status:</label>
                        <select id="status" name="status" onchange="this.form.submit()" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md transition-colors">
                            <option value="">All Statuses</option>
                            <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Approved</option>
                            <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                </form>
            </div>

            <!-- List/Table View -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Submission Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Leave Type
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Duration
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
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
                                <tr class="hover:bg-gray-50 transition-colors cursor-pointer toggle-row" data-target="details-{{ $leave->id }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $leave->created_at->format('M d, Y') }}
                                        <div class="text-xs text-gray-500">{{ $leave->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $leave->jenis_izin }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $start->format('M d') }} - {{ $end->format('M d, Y') }}
                                        <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $days }} {{ Str::plural('day', $days) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if (strtolower($leave->status_approval) === 'approved')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                                                Approved
                                            </span>
                                        @elseif (strtolower($leave->status_approval) === 'rejected')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                                                Rejected
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-yellow-400 animate-pulse" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button class="text-indigo-600 hover:text-indigo-900">
                                            View Details
                                            <svg class="w-4 h-4 inline transform transition-transform duration-200 arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Expandable Details Row -->
                                <tr id="details-{{ $leave->id }}" class="hidden bg-gray-50/50 border-b border-gray-200 w-full transition-all duration-300 ease-in-out">
                                    <td colspan="5" class="px-6 py-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <h4 class="text-sm font-bold text-gray-900 mb-2">Reason for Absence</h4>
                                                <p class="text-sm text-gray-700 p-3 bg-white rounded-md border border-gray-200 shadow-sm whitespace-pre-wrap">{{ $leave->alasan }}</p>
                                                
                                                @if(strtolower($leave->status_approval) === 'rejected')
                                                    <h4 class="text-sm font-bold text-red-600 mt-4 mb-2">Rejection Reason</h4>
                                                    <p class="text-sm text-red-700 p-3 bg-red-50 rounded-md border border-red-200 shadow-sm whitespace-pre-wrap">{{ $leave->rejection_reason ?? 'No detailed reason provided.' }}</p>
                                                @endif
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-bold text-gray-900 mb-2">Supporting Document</h4>
                                                @if ($leave->bukti_file)
                                                    <div class="flex items-center space-x-3 p-3 bg-white rounded-md border border-gray-200 shadow-sm">
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
                                                            <a href="{{ Storage::url($leave->bukti_file) }}" target="_blank" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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
                                        <a href="{{ route('leaves.create') }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800 font-medium">Submit your first request &rarr;</a>
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