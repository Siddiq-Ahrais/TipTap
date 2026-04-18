<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Request Leave / Absence') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden pb-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Absence Request Form
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Please fill out the form below to submit your leave request. Ensure all details are accurate.
                    </p>
                </div>

                <div class="px-6 mt-6">
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <strong>There were some issues with your submission:</strong>
                            <ul class="list-disc mt-2 ml-4 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('leaves.store') }}" method="POST" enctype="multipart/form-data" id="leaveForm" class="space-y-6">
                        @csrf

                        <!-- Leave Type -->
                        <div>
                            <label for="jenis_izin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Leave Type / Category <span class="text-red-500">*</span></label>
                            <select id="jenis_izin" name="jenis_izin" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="" disabled selected>Select category...</option>
                                <option value="Cuti Tahunan" {{ old('jenis_izin') == 'Cuti Tahunan' ? 'selected' : '' }}>Cuti Tahunan (Annual Leave)</option>
                                <option value="Sakit" {{ old('jenis_izin') == 'Sakit' ? 'selected' : '' }}>Sakit (Sick Leave)</option>
                                <option value="Keperluan Mendadak" {{ old('jenis_izin') == 'Keperluan Mendadak' ? 'selected' : '' }}>Keperluan Mendadak (Emergency)</option>
                            </select>
                            <p class="mt-1 text-sm text-red-600 hidden" id="error-jenis_izin">Please select a leave type.</p>
                        </div>

                        <!-- Dates Row -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date <span class="text-red-500">*</span></label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                <p class="mt-1 text-sm text-red-600 hidden" id="error-tanggal_mulai">Start date is required.</p>
                            </div>

                            <div>
                                <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date <span class="text-red-500">*</span></label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required class="pl-10 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                <p class="mt-1 text-sm text-red-600 hidden" id="error-tanggal_selesai">End date must be equal or later than start date.</p>
                            </div>
                        </div>

                        <!-- Reason -->
                        <div>
                            <label for="alasan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reason / Description <span class="text-red-500">*</span></label>
                            <div class="mt-1">
                                <textarea id="alasan" name="alasan" rows="4" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Provide a brief explanation for your absence...">{{ old('alasan') }}</textarea>
                            </div>
                            <p class="mt-1 text-sm text-red-600 hidden" id="error-alasan">Please provide a reason.</p>
                        </div>

                        <!-- File Upload Component -->
                        <div id="fileUploadContainer" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Medical Certificate / Supporting Document <span class="text-red-500" id="fileRequiredStar">*</span></label>
                            
                            <div id="drop-area" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md transition-colors hover:border-indigo-400 hover:bg-indigo-50 dark:border-gray-600 dark:hover:border-indigo-500 dark:hover:bg-gray-700 relative">
                                <!-- Loading Overlay -->
                                <div id="uploadLoader" class="absolute inset-0 bg-white/80 dark:bg-gray-800/80 flex flex-col items-center justify-center hidden rounded-md z-10">
                                    <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-600 font-medium">Processing file...</p>
                                </div>

                                <div class="space-y-1 text-center" id="upload-prompt">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                                        <label for="bukti_file" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Upload a file</span>
                                            <input id="bukti_file" name="bukti_file" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        PDF, PNG, JPG up to 5MB
                                    </p>
                                </div>

                                <!-- Preview Area -->
                                <div id="preview-area" class="hidden w-full flex items-center justify-between bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                    <div class="flex items-center space-x-3 overflow-hidden">
                                        <div id="file-icon" class="flex-shrink-0 h-10 w-10 text-gray-400 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center overflow-hidden">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <div class="truncate">
                                            <p id="file-name" class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate"></p>
                                            <p id="file-size" class="text-xs text-gray-500 dark:text-gray-400"></p>
                                        </div>
                                    </div>
                                    <button type="button" id="remove-file" class="ml-4 flex-shrink-0 bg-red-100 hover:bg-red-200 text-red-600 p-2 rounded-full transition-colors focus:outline-none">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                            <p class="mt-1 text-sm text-red-600 hidden" id="error-file">Please upload a valid file (Max 5MB).</p>
                        </div>

                        <div class="pt-5 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                            <a href="{{ route('leaves.index') }}" class="bg-white dark:bg-gray-800 py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Client-Side Validation and Upload Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('leaveForm');
            const jenisIzinSelect = document.getElementById('jenis_izin');
            const startDateInput = document.getElementById('tanggal_mulai');
            const endDateInput = document.getElementById('tanggal_selesai');
            const alasanInput = document.getElementById('alasan');
            const fileInput = document.getElementById('bukti_file');
            
            const fileUploadContainer = document.getElementById('fileUploadContainer');
            const dropArea = document.getElementById('drop-area');
            const uploadPrompt = document.getElementById('upload-prompt');
            const previewArea = document.getElementById('preview-area');
            const fileNameDisplay = document.getElementById('file-name');
            const fileSizeDisplay = document.getElementById('file-size');
            const fileIcon = document.getElementById('file-icon');
            const removeFileBtn = document.getElementById('remove-file');
            const uploadLoader = document.getElementById('uploadLoader');

            const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

            // Show file upload if needed initially
            toggleFileUpload();

            jenisIzinSelect.addEventListener('change', toggleFileUpload);

            function toggleFileUpload() {
                // In Indonesian options, "Sakit" requires file. Or allow optional for others.
                if (jenisIzinSelect.value === 'Sakit') {
                    fileUploadContainer.classList.remove('hidden');
                    document.getElementById('fileRequiredStar').classList.remove('hidden');
                } else if (jenisIzinSelect.value !== '') {
                    fileUploadContainer.classList.remove('hidden'); 
                    document.getElementById('fileRequiredStar').classList.add('hidden');
                } else {
                    fileUploadContainer.classList.add('hidden');
                }
            }

            // Client Validation Logic
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Clear errors
                document.querySelectorAll('[id^="error-"]').forEach(el => el.classList.add('hidden'));

                if (!jenisIzinSelect.value) {
                    document.getElementById('error-jenis_izin').classList.remove('hidden');
                    isValid = false;
                }

                if (!startDateInput.value) {
                    document.getElementById('error-tanggal_mulai').classList.remove('hidden');
                    isValid = false;
                }

                if (!endDateInput.value) {
                    document.getElementById('error-tanggal_selesai').classList.remove('hidden');
                    isValid = false;
                }

                if (startDateInput.value && endDateInput.value) {
                    const start = new Date(startDateInput.value);
                    const end = new Date(endDateInput.value);
                    if (end < start) {
                        document.getElementById('error-tanggal_selesai').innerText = "End date cannot be earlier than start date.";
                        document.getElementById('error-tanggal_selesai').classList.remove('hidden');
                        isValid = false;
                    }
                }

                if (!alasanInput.value.trim()) {
                    document.getElementById('error-alasan').classList.remove('hidden');
                    isValid = false;
                }

                if (jenisIzinSelect.value === 'Sakit' && !fileInput.files.length) {
                    document.getElementById('error-file').innerText = "Medical certificate is required for Sick Leave.";
                    document.getElementById('error-file').classList.remove('hidden');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                }
            });

            // Drag and Drop Logic
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => {
                    dropArea.classList.add('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/20');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => {
                    dropArea.classList.remove('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/20');
                }, false);
            });

            dropArea.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files.length) {
                    fileInput.files = files;
                    validateAndPreviewFile(files[0]);
                }
            }

            fileInput.addEventListener('change', function() {
                if (this.files.length) {
                    validateAndPreviewFile(this.files[0]);
                }
            });

            function validateAndPreviewFile(file) {
                document.getElementById('error-file').classList.add('hidden');
                
                // Validate size
                if (file.size > MAX_FILE_SIZE) {
                    document.getElementById('error-file').innerText = "File size exceeds 5MB limit.";
                    document.getElementById('error-file').classList.remove('hidden');
                    resetFile();
                    return;
                }

                // Validate type
                const validTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
                if (!validTypes.includes(file.type)) {
                    document.getElementById('error-file').innerText = "Invalid file type. Only PDF, JPG, and PNG are allowed.";
                    document.getElementById('error-file').classList.remove('hidden');
                    resetFile();
                    return;
                }

                // Fake loader for visual feedback
                uploadLoader.classList.remove('hidden');
                
                setTimeout(() => {
                    uploadLoader.classList.add('hidden');
                    showPreview(file);
                }, 600); // 600ms fake processing time
            }

            function showPreview(file) {
                uploadPrompt.classList.add('hidden');
                previewArea.classList.remove('hidden');
                
                fileNameDisplay.textContent = file.name;
                fileSizeDisplay.textContent = formatBytes(file.size);

                // Show thumbnail if image
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        fileIcon.innerHTML = `<img src="${e.target.result}" class="h-full w-full object-cover rounded" alt="preview">`;
                    }
                    reader.readAsDataURL(file);
                } else {
                    // PDF icon
                    fileIcon.innerHTML = `<svg class="h-6 w-6 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>`;
                }
            }

            removeFileBtn.addEventListener('click', function() {
                resetFile();
            });

            function resetFile() {
                fileInput.value = '';
                uploadPrompt.classList.remove('hidden');
                previewArea.classList.add('hidden');
                fileIcon.innerHTML = `<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>`;
            }

            function formatBytes(bytes, decimals = 2) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const dm = decimals < 0 ? 0 : decimals;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
            }
        });
    </script>
</x-app-layout>