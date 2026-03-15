@extends('layouts.app')

@section('title', 'Request a Role')
@section('page-title', 'Request a Role')
@section('page-description', 'Submit a request to become a Doctor, Store, or MR on our platform')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="flex items-start gap-3 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
        <i class="fas fa-check-circle mt-0.5 text-green-500"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">
        <i class="fas fa-exclamation-circle mt-0.5 text-red-500"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif
    @if(session('info'))
    <div class="flex items-start gap-3 p-4 bg-blue-50 border border-blue-200 rounded-lg text-blue-800">
        <i class="fas fa-info-circle mt-0.5 text-blue-500"></i>
        <span>{{ session('info') }}</span>
    </div>
    @endif

    {{-- My Existing Requests --}}
    @if($myRequests->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-history text-gray-500"></i>
                My Role Requests
            </h2>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($myRequests as $req)
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center
                        @if($req->requested_role === 'Doctor') bg-blue-100 text-blue-600
                        @elseif($req->requested_role === 'Store') bg-purple-100 text-purple-600
                        @else bg-orange-100 text-orange-600 @endif">
                        <i class="fas {{ $req->role_icon }} text-sm"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">{{ $req->requested_role }} Role</p>
                        <p class="text-xs text-gray-500">{{ $req->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($req->admin_note)
                    <span class="text-xs text-gray-500 italic max-w-xs truncate" title="{{ $req->admin_note }}">
                        "{{ Str::limit($req->admin_note, 40) }}"
                    </span>
                    @endif
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $req->status_badge_class }}">
                        {{ ucfirst($req->status) }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Existing pending block notice --}}
    @if($existingRequest)
    <div class="flex items-start gap-3 p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-800">
        <i class="fas fa-clock mt-0.5 text-yellow-500"></i>
        <div>
            <p class="font-medium">You already have a pending {{ $existingRequest->requested_role }} request.</p>
            <p class="text-sm mt-1">Submitted {{ $existingRequest->created_at->diffForHumans() }}. Please wait for admin review before submitting another.</p>
        </div>
    </div>
    @endif

    {{-- Role Selection Cards --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-900">Submit a Role Request</h2>
            <p class="text-sm text-gray-500 mt-1">Select the role you want to apply for and provide supporting details.</p>
        </div>

        <form action="{{ route('role-requests.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            {{-- Role Cards --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Select Role <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                    {{-- Doctor --}}
                    <label class="role-card cursor-pointer">
                        <input type="radio" name="requested_role" value="Doctor" class="sr-only peer"
                               {{ ($preselectedRole === 'Doctor' || old('requested_role') === 'Doctor') ? 'checked' : '' }}>
                        <div class="border-2 rounded-xl p-5 text-center transition-all
                                    peer-checked:border-blue-500 peer-checked:bg-blue-50
                                    hover:border-blue-300 hover:bg-blue-50/50 border-gray-200">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-user-md text-blue-600 text-xl"></i>
                            </div>
                            <p class="font-bold text-gray-900">Doctor</p>
                            <p class="text-xs text-gray-500 mt-1">Veterinary / Medical Professional</p>
                        </div>
                    </label>

                    {{-- Store --}}
                    <label class="role-card cursor-pointer">
                        <input type="radio" name="requested_role" value="Store" class="sr-only peer"
                               {{ ($preselectedRole === 'Store' || old('requested_role') === 'Store') ? 'checked' : '' }}>
                        <div class="border-2 rounded-xl p-5 text-center transition-all
                                    peer-checked:border-purple-500 peer-checked:bg-purple-50
                                    hover:border-purple-300 hover:bg-purple-50/50 border-gray-200">
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-store text-purple-600 text-xl"></i>
                            </div>
                            <p class="font-bold text-gray-900">Store</p>
                            <p class="text-xs text-gray-500 mt-1">Pharmacy / Veterinary Store</p>
                        </div>
                    </label>

                    {{-- MR --}}
                    <label class="role-card cursor-pointer">
                        <input type="radio" name="requested_role" value="MR" class="sr-only peer"
                               {{ ($preselectedRole === 'MR' || old('requested_role') === 'MR') ? 'checked' : '' }}>
                        <div class="border-2 rounded-xl p-5 text-center transition-all
                                    peer-checked:border-orange-500 peer-checked:bg-orange-50
                                    hover:border-orange-300 hover:bg-orange-50/50 border-gray-200">
                            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-briefcase text-orange-600 text-xl"></i>
                            </div>
                            <p class="font-bold text-gray-900">MR</p>
                            <p class="text-xs text-gray-500 mt-1">Medical Representative</p>
                        </div>
                    </label>

                </div>
                @error('requested_role')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Message --}}
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">
                    Message / Additional Details
                    <span class="text-gray-400 font-normal">(optional)</span>
                </label>
                <textarea id="message" name="message" rows="4"
                          class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none"
                          placeholder="Tell us about your experience, clinic/store name, registration number, etc.">{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Document Upload --}}
            <div>
                <label for="document" class="block text-sm font-medium text-gray-700 mb-1">
                    Supporting Document
                    <span class="text-gray-400 font-normal">(optional — PDF, JPG, PNG, max 2MB)</span>
                </label>
                <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-green-400 transition-colors">
                    <input type="file" id="document" name="document" accept=".pdf,.jpg,.jpeg,.png"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                    <p class="text-sm text-gray-500" id="file-label">Click to upload or drag and drop</p>
                    <p class="text-xs text-gray-400 mt-1">Registration certificate, license, etc.</p>
                </div>
                @error('document')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    <i class="fas fa-paper-plane mr-2"></i>Submit Request
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    // Show filename when file is selected
    document.getElementById('document').addEventListener('change', function() {
        const label = document.getElementById('file-label');
        if (this.files.length > 0) {
            label.textContent = this.files[0].name;
            label.classList.add('text-green-600', 'font-medium');
        } else {
            label.textContent = 'Click to upload or drag and drop';
            label.classList.remove('text-green-600', 'font-medium');
        }
    });
</script>
@endsection
