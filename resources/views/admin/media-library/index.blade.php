@extends('layouts.master')

@section('title', 'Media Library')

@section('content')
<div class="p-6 max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-images text-blue-600"></i>
                Media Library
            </h1>
            <p class="text-sm text-gray-500 mt-1">Upload and manage images and documents for your website.</p>
        </div>
    </div>

    {{-- Upload Area --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Upload Files</h2>
            <span class="text-xs text-gray-400">Max file size: 10MB</span>
        </div>
        <form id="upload-form" action="{{ route('admin.media-library.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-400 transition-colors cursor-pointer" id="drop-zone">
                <input type="file" name="file" id="file-input" class="hidden" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">
                <div class="space-y-2">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400"></i>
                    <p class="text-gray-600">Drag and drop files here, or <span class="text-blue-600 hover:underline">browse</span></p>
                    <p class="text-xs text-gray-400">Supports: Images, PDFs, Documents</p>
                </div>
            </div>
            <div id="file-preview" class="hidden">
                <div class="flex items-center justify-between bg-gray-50 rounded-lg px-4 py-3">
                    <span id="file-name" class="text-sm text-gray-700"></span>
                    <button type="button" id="remove-file" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <button type="submit" class="mt-4 w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-upload"></i> Upload File
                </button>
            </div>
        </form>
    </div>

    {{-- Filters --}}
    <div class="flex items-center gap-4">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.media-library.index', ['type' => 'image']) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $type === 'image' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
                <i class="fas fa-image mr-1"></i> Images
            </a>
            <a href="{{ route('admin.media-library.index', ['type' => 'document']) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $type === 'document' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
                <i class="fas fa-file mr-1"></i> Documents
            </a>
        </div>
        <form method="GET" class="flex-1 max-w-md">
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search files..."
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
            <input type="hidden" name="type" value="{{ $type }}">
        </form>
    </div>

    {{-- Media Grid --}}
    @if($media->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($media as $item)
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden group relative hover:shadow-lg transition-shadow">
                    <div class="aspect-square bg-gray-100 flex items-center justify-center">
                        @if($item->file_type === 'image')
                            <img src="{{ $item->url }}" alt="{{ $item->alt_text }}" class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-file-alt text-4xl text-gray-400"></i>
                        @endif
                    </div>
                    <div class="p-2">
                        <p class="text-xs text-gray-700 truncate" title="{{ $item->file_name }}">{{ $item->file_name }}</p>
                        <p class="text-xs text-gray-400">{{ $item->formatted_size }}</p>
                    </div>
                    {{-- Actions Overlay --}}
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                        <button type="button" onclick="copyToClipboard('{{ $item->url }}')"
                                class="w-8 h-8 bg-white rounded-lg flex items-center justify-center hover:bg-gray-100" title="Copy URL">
                            <i class="fas fa-link text-gray-700 text-sm"></i>
                        </button>
                        <a href="{{ $item->url }}" target="_blank"
                           class="w-8 h-8 bg-white rounded-lg flex items-center justify-center hover:bg-gray-100" title="View">
                            <i class="fas fa-eye text-gray-700 text-sm"></i>
                        </a>
                        <form action="{{ route('admin.media-library.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Delete this file?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-8 h-8 bg-white rounded-lg flex items-center justify-center hover:bg-red-100" title="Delete">
                                <i class="fas fa-trash text-red-500 text-sm"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $media->links() }}
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
            <i class="fas fa-images text-5xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No media files</h3>
            <p class="text-gray-500">Upload your first file using the area above.</p>
        </div>
    @endif

</div>

@push('scripts')
<script>
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-input');
    const filePreview = document.getElementById('file-preview');
    const fileName = document.getElementById('file-name');
    const removeFile = document.getElementById('remove-file');

    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-blue-400', 'bg-blue-50');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-blue-400', 'bg-blue-50');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-blue-400', 'bg-blue-50');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            showPreview(e.dataTransfer.files[0]);
        }
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length) {
            showPreview(fileInput.files[0]);
        }
    });

    removeFile.addEventListener('click', () => {
        fileInput.value = '';
        filePreview.classList.add('hidden');
    });

    function showPreview(file) {
        fileName.textContent = file.name;
        filePreview.classList.remove('hidden');
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('URL copied to clipboard!');
        });
    }
</script>
@endpush
@endsection
