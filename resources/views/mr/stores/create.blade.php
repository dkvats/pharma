@extends('layouts.app')

@section('title', 'Register New Store')
@section('page-title', 'Register New Store / Distributor')

@php
    $isAdminCreator = auth()->user()->hasAnyRole(['Admin', 'Super Admin']);
    $storeIndexRoute = $isAdminCreator ? 'admin.stores.approval.index' : 'mr.stores.index';
    $storeCreateRoute = $isAdminCreator ? 'admin.stores.store' : 'mr.stores.store';
    $districtRouteName = $isAdminCreator ? 'admin.stores.get-districts' : 'mr.stores.get-districts';
    $cityRouteName = $isAdminCreator ? 'admin.stores.get-cities' : 'mr.stores.get-cities';
    $areaRouteName = $isAdminCreator ? 'admin.stores.get-areas' : 'mr.stores.get-areas';
@endphp

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Add Store / Distributor</h1>
        <p class="text-gray-600 mt-2">Complete store registration with all required details and optional compliance documents.</p>
        <div class="mt-4">
            <a href="{{ route($storeIndexRoute) }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i class="fas fa-list mr-2"></i>View & Update Stores
            </a>
        </div>
    </div>

    <form action="{{ route($storeCreateRoute) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- Alert Messages -->
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-3"></i>
                    <div>
                        <h3 class="text-red-900 font-medium mb-2">Please correct the following errors:</h3>
                        <ul class="text-red-700 text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- SECTION A: Basic Store Details -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-6">
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-store text-blue-600"></i>
                </div>
                <h2 class="ml-3 text-xl font-semibold text-gray-900">Basic Store Details</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Store Name -->
                <div>
                    <label for="store_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Store Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="store_name" name="store_name" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('store_name') border-red-500 @enderror"
                        placeholder="e.g., ABC Veterinary Clinic" value="{{ old('store_name') }}">
                    @error('store_name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Store Type -->
                <div>
                    <label for="store_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Store Type <span class="text-gray-500 text-xs">(Optional)</span>
                    </label>
                    <select id="store_type" name="store_type"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('store_type') border-red-500 @enderror">
                        <option value="">Select store type...</option>
                        <option value="retail" {{ old('store_type') === 'retail' ? 'selected' : '' }}>Retail Store</option>
                        <option value="distributor" {{ old('store_type') === 'distributor' ? 'selected' : '' }}>Distributor</option>
                        <option value="clinic" {{ old('store_type') === 'clinic' ? 'selected' : '' }}>Veterinary Clinic</option>
                        <option value="pharmacy" {{ old('store_type') === 'pharmacy' ? 'selected' : '' }}>Pharmacy</option>
                    </select>
                    @error('store_type')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Owner Name -->
                <div>
                    <label for="owner_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Owner Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="owner_name" name="owner_name" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('owner_name') border-red-500 @enderror"
                        placeholder="Full name of store owner" value="{{ old('owner_name') }}">
                    @error('owner_name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Mobile Number -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Mobile Number <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                            <i class="fas fa-phone text-sm"></i>
                        </span>
                        <input type="tel" id="phone" name="phone" required maxlength="10"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                            placeholder="10-digit mobile number" inputmode="numeric" value="{{ old('phone') }}">
                    </div>
                    @error('phone')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Alternate Mobile -->
                <div>
                    <label for="alt_phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Alternate Mobile <span class="text-gray-500 text-xs">(Optional)</span>
                    </label>
                    <input type="tel" id="alt_phone" name="alt_phone" maxlength="10"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('alt_phone') border-red-500 @enderror"
                        placeholder="Alternate phone number" inputmode="numeric" value="{{ old('alt_phone') }}">
                    @error('alt_phone')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-gray-500 text-xs">(Optional)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                            <i class="fas fa-envelope text-sm"></i>
                        </span>
                        <input type="email" id="email" name="email"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                            placeholder="store@example.com" value="{{ old('email') }}">
                    </div>
                    @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- SECTION B: Owner Identity -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-6">
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                    <i class="fas fa-id-card text-indigo-600"></i>
                </div>
                <h2 class="ml-3 text-xl font-semibold text-gray-900">Owner Identity</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Aadhaar Number -->
                <div>
                    <label for="aadhaar" class="block text-sm font-medium text-gray-700 mb-2">
                        Aadhaar Number <span class="text-gray-500 text-xs">(Optional)</span>
                    </label>
                    <input type="text" id="aadhaar" name="aadhaar" maxlength="12"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('aadhaar') border-red-500 @enderror"
                        placeholder="12-digit Aadhaar number" inputmode="numeric" value="{{ old('aadhaar') }}">
                    <p class="text-gray-500 text-xs mt-1">12 digits only, e.g., 123456789012</p>
                    @error('aadhaar')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Owner Photo -->
                <div>
                    <label for="owner_photo" class="block text-sm font-medium text-gray-700 mb-2">
                        Owner Photo <span class="text-gray-500 text-xs">(JPG/PNG, Max 2MB)</span>
                    </label>
                    <div class="relative">
                        <input type="file" id="owner_photo" name="owner_photo" accept="image/jpeg,image/png"
                            class="hidden" onchange="previewImage(this, 'owner_photo_preview')">
                        <label for="owner_photo" class="flex items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition">
                            <div id="owner_photo_label" class="text-center">
                                <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-600">Click to upload photo</p>
                            </div>
                        </label>
                        <img id="owner_photo_preview" class="hidden mt-2 h-24 w-24 object-cover rounded border border-gray-300">
                    </div>
                    @error('owner_photo')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Store Photo -->
                <div>
                    <label for="store_photo" class="block text-sm font-medium text-gray-700 mb-2">
                        Store Photo <span class="text-gray-500 text-xs">(JPG/PNG, Max 2MB)</span>
                    </label>
                    <div class="relative">
                        <input type="file" id="store_photo" name="store_photo" accept="image/jpeg,image/png"
                            class="hidden" onchange="previewImage(this, 'store_photo_preview')">
                        <label for="store_photo" class="flex items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
                            <div id="store_photo_label" class="text-center">
                                <i class="fas fa-image text-2xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-600">Click to upload photo</p>
                            </div>
                        </label>
                        <img id="store_photo_preview" class="hidden mt-2 h-24 w-24 object-cover rounded border border-gray-300">
                    </div>
                    @error('store_photo')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- SECTION D: Address & Location Details -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-6">
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="fas fa-map-marker-alt text-green-600"></i>
                </div>
                <h2 class="ml-3 text-xl font-semibold text-gray-900">Address & Location</h2>
            </div>

            <!-- PIN Code Lookup -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <label for="pincode" class="block text-sm font-medium text-blue-900 mb-3">
                    <i class="fas fa-lightbulb text-blue-600 mr-2"></i>PIN Code (Auto-fill Location)
                </label>
                <div class="flex gap-2">
                    <input type="text" id="pincode" name="pincode" maxlength="6" required
                        placeholder="Enter 6-digit PIN code"
                        class="flex-1 px-4 py-2.5 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('pincode') border-red-500 @enderror"
                        inputmode="numeric" value="{{ old('pincode') }}">
                    <button type="button" onclick="lookupPinCode(document.getElementById('pincode').value)"
                        class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        <i class="fas fa-search mr-2"></i>Lookup
                    </button>
                </div>
                <div id="pin_status" class="text-sm mt-3"></div>
                
                <!-- Manual Fallback - shown when PIN not found -->
                <div id="manual_fallback" class="hidden mt-4 p-4 bg-amber-50 border border-amber-300 rounded-lg">
                    <p class="text-amber-800 text-sm font-medium mb-3">
                        <i class="fas fa-hand-point-up mr-2"></i>PIN code not found? Select your location manually:
                    </p>
                    <button type="button" onclick="enableManualEntry()"
                        class="w-full px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition">
                        <i class="fas fa-edit mr-2"></i>Select State & District Manually
                    </button>
                </div>
                
                @error('pincode')<p class="text-red-500 text-sm mt-2">{{ $message }}</p>@enderror
            </div>

            <!-- Location Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- State -->
                <div>
                    <label for="state_id" class="block text-sm font-medium text-gray-700 mb-2">
                        State <span class="text-red-500">*</span>
                    </label>
                    <select id="state_id" name="state_id" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('state_id') border-red-500 @enderror">
                        <option value="">Select State</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}" data-name="{{ $state->name }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                        @endforeach
                    </select>
                    @error('state_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- District -->
                <div>
                    <label for="district_id" class="block text-sm font-medium text-gray-700 mb-2">
                        District <span class="text-red-500">*</span>
                    </label>
                    <select id="district_id" name="district_id" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('district_id') border-red-500 @enderror">
                        <option value="">Select District</option>
                    </select>
                    @error('district_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- City -->
                <div>
                    <label for="city_id" class="block text-sm font-medium text-gray-700 mb-2">
                        City <span class="text-red-500">*</span>
                    </label>
                    <select id="city_id" name="city_id" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('city_id') border-red-500 @enderror">
                        <option value="">Select City</option>
                    </select>
                    @error('city_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Area / Locality -->
                <div>
                    <label for="area_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Locality / Area <span class="text-red-500">*</span>
                    </label>
                    <select id="area_id" name="area_id" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('area_id') border-red-500 @enderror">
                        <option value="">Select Locality / Area</option>
                    </select>
                    @error('area_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Full Address -->
            <div class="mt-6">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                    Full Address <span class="text-red-500">*</span>
                </label>
                <textarea id="address" name="address" rows="3" required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-500 @enderror"
                    placeholder="Enter complete address (street, building, landmark, etc.)">{{ old('address') }}</textarea>
                @error('address')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <!-- SECTION E: Business & Compliance Details -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-6">
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-file-contract text-purple-600"></i>
                </div>
                <h2 class="ml-3 text-xl font-semibold text-gray-900">Business & Compliance</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- GST Number -->
                <div>
                    <label for="gst_no" class="block text-sm font-medium text-gray-700 mb-2">
                        GST Number <span class="text-gray-500 text-xs">(Optional)</span>
                    </label>
                    <input type="text" id="gst_no" name="gst_no" maxlength="15"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('gst_no') border-red-500 @enderror"
                        placeholder="15-character GST number" value="{{ old('gst_no') }}">
                    <p class="text-gray-500 text-xs mt-1">Format: 2 digits state + 5 chars + 4 digits + 1 check char</p>
                    @error('gst_no')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- PAN Number -->
                <div>
                    <label for="pan_no" class="block text-sm font-medium text-gray-700 mb-2">
                        PAN Number <span class="text-gray-500 text-xs">(Optional)</span>
                    </label>
                    <input type="text" id="pan_no" name="pan_no" maxlength="10" class="uppercase"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('pan_no') border-red-500 @enderror"
                        placeholder="10-character PAN number" value="{{ old('pan_no') }}">
                    <p class="text-gray-500 text-xs mt-1">Format: AAAAA0000A (5 letters + 4 digits + 1 letter)</p>
                    @error('pan_no')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Drug License Number -->
                <div>
                    <label for="drug_license_no" class="block text-sm font-medium text-gray-700 mb-2">
                        Drug License Number <span class="text-gray-500 text-xs">(Optional)</span>
                    </label>
                    <input type="text" id="drug_license_no" name="drug_license_no"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('drug_license_no') border-red-500 @enderror"
                        placeholder="License number from state pharmacy board" value="{{ old('drug_license_no') }}">
                    @error('drug_license_no')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- License Expiry Date -->
                <div>
                    <label for="license_expiry" class="block text-sm font-medium text-gray-700 mb-2">
                        License Expiry Date <span class="text-gray-500 text-xs">(Optional)</span>
                    </label>
                    <input type="date" id="license_expiry" name="license_expiry"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('license_expiry') border-red-500 @enderror"
                        value="{{ old('license_expiry') }}">
                    @error('license_expiry')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- SECTION G: Financial Settings -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-6">
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                    <i class="fas fa-hand-holding-usd text-yellow-600"></i>
                </div>
                <h2 class="ml-3 text-xl font-semibold text-gray-900">Financial Settings</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Default Discount -->
                <div>
                    <label for="default_discount" class="block text-sm font-medium text-gray-700 mb-2">
                        Default Discount (%) <span class="text-gray-500 text-xs">(Optional)</span>
                    </label>
                    <div class="relative">
                        <input type="number" id="default_discount" name="default_discount" min="0" max="100" step="0.01"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('default_discount') border-red-500 @enderror"
                            placeholder="0.00" value="{{ old('default_discount', 0) }}">
                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500">%</span>
                    </div>
                    @error('default_discount')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Credit Limit -->
                <div>
                    <label for="credit_limit" class="block text-sm font-medium text-gray-700 mb-2">
                        Credit Limit (₹) <span class="text-gray-500 text-xs">(Optional)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">₹</span>
                        <input type="number" id="credit_limit" name="credit_limit" min="0" step="100"
                            class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('credit_limit') border-red-500 @enderror"
                            placeholder="0.00" value="{{ old('credit_limit', 0) }}">
                    </div>
                    @error('credit_limit')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Payment Terms -->
                <div>
                    <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-2">
                        Payment Terms <span class="text-gray-500 text-xs">(Optional)</span>
                    </label>
                    <select id="payment_terms" name="payment_terms"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('payment_terms') border-red-500 @enderror">
                        <option value="">Select payment terms...</option>
                        <option value="Immediate" {{ old('payment_terms') === 'Immediate' ? 'selected' : '' }}>Immediate</option>
                        <option value="Net 7 days" {{ old('payment_terms') === 'Net 7 days' ? 'selected' : '' }}>Net 7 Days</option>
                        <option value="Net 15 days" {{ old('payment_terms') === 'Net 15 days' ? 'selected' : '' }}>Net 15 Days</option>
                        <option value="Net 30 days" {{ old('payment_terms') === 'Net 30 days' ? 'selected' : '' }}>Net 30 Days</option>
                        <option value="Net 45 days" {{ old('payment_terms') === 'Net 45 days' ? 'selected' : '' }}>Net 45 Days</option>
                        <option value="Net 60 days" {{ old('payment_terms') === 'Net 60 days' ? 'selected' : '' }}>Net 60 Days</option>
                    </select>
                    @error('payment_terms')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end gap-4 pt-6 border-t">
            <a href="{{ route('mr.stores.index') }}"
                class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit"
                class="px-8 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition flex items-center">
                <i class="fas fa-check mr-2"></i>Register Store
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
// ==================== HELPER: Get correct API URLs ====================
// Use route() to generate full URLs that work regardless of where app is installed
window.getPincodeUrl = function(pin) {
    // Generate URL using Laravel's route helper
    const baseUrl = '{{ route("api.pincode.lookup", ["pin" => "PIN_TEMP"]) }}';
    return baseUrl.replace('PIN_TEMP', pin);
};

window.getDistrictsUrl = function(stateId) {
    const baseUrl = '{{ route($districtRouteName, ["stateId" => "STATE_TEMP"]) }}';
    return baseUrl.replace('STATE_TEMP', stateId);
};

window.getCitiesUrl = function(districtId) {
    const baseUrl = '{{ route($cityRouteName, ["districtId" => "DIST_TEMP"]) }}';
    return baseUrl.replace('DIST_TEMP', districtId);
};

window.getAreasUrl = function(cityId) {
    const baseUrl = '{{ route($areaRouteName, ["cityId" => "CITY_TEMP"]) }}';
    return baseUrl.replace('CITY_TEMP', cityId);
};

// ==================== PIN CODE LOOKUP ====================
// Define functions immediately so onclick handlers can find them
window.lookupPinCode = function(pinValue) {
    const pin = (pinValue || document.getElementById('pincode').value || '').trim();
    const statusEl = document.getElementById('pin_status');
    const fallbackEl = document.getElementById('manual_fallback');
    
    // Validate PIN format
    if (!pin || pin.length !== 6 || !/^\d{6}$/.test(pin)) {
        window.showStatus('Please enter a valid 6-digit PIN code', 'error');
        fallbackEl?.classList.add('hidden');
        return;
    }
    
    // Show loading state
    window.showStatus('Looking up PIN code...', 'loading');
    
    // Build full URL to API endpoint using Laravel route
    const fetchUrl = window.getPincodeUrl(pin);
    console.log('Fetching PIN from:', fetchUrl);
    
    fetch(fetchUrl)
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('API Response:', data);
            
            // Check if successful
            if (data.success && data.data) {
                handlePincodeSuccess(data.data, fallbackEl, statusEl);
            } else {
                window.showStatus('PIN code not found. Please select manually.', 'error');
                fallbackEl?.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            window.showStatus('Error looking up PIN code. Try manual selection.', 'error');
            fallbackEl?.classList.remove('hidden');
        });
}

function handlePincodeSuccess(pinData, fallbackEl, statusEl) {
    console.log('PIN Data received:', pinData);
    window.showStatus('✓ PIN found! Auto-filling location...', 'success');
    fallbackEl?.classList.add('hidden');

    const stateSelect = document.getElementById('state_id');
    const districtSelect = document.getElementById('district_id');
    const citySelect = document.getElementById('city_id');
    const areaSelect = document.getElementById('area_id');

    const territory = pinData.territory || {};
    const stateId = territory.state_id ? String(territory.state_id) : null;
    const districtId = territory.district_id ? String(territory.district_id) : null;
    const cityId = territory.city_id ? String(territory.city_id) : null;
    const areaId = territory.area_id ? String(territory.area_id) : null;

    const stateName = (pinData.state || '').trim().toLowerCase();
    const districtName = (pinData.district || '').trim().toLowerCase();
    const areaName = (pinData.area || pinData.post_office || '').trim().toLowerCase();

    const stateOption = stateId
        ? Array.from(stateSelect.options).find(opt => opt.value === stateId)
        : Array.from(stateSelect.options).find(opt => opt.text.trim().toLowerCase() === stateName);

    if (!stateOption) {
        console.warn('State not found for PIN:', pinData.state, territory);
        window.showStatus('State not found. Please select manually.', 'warning');
        fallbackEl?.classList.remove('hidden');
        return;
    }

    stateSelect.value = stateOption.value;

    loadDistricts(stateOption.value).then(() => {
        const districtOption = districtId
            ? Array.from(districtSelect.options).find(opt => opt.value === districtId)
            : Array.from(districtSelect.options).find(opt => opt.text.trim().toLowerCase() === districtName);

        if (!districtOption) {
            console.warn('District not found for PIN:', pinData.district, territory);
            return;
        }

        districtSelect.value = districtOption.value;

        loadCities(districtOption.value).then(() => {
            const cityOption = cityId
                ? Array.from(citySelect.options).find(opt => opt.value === cityId)
                : Array.from(citySelect.options).find(opt => opt.text.trim().toLowerCase().includes(districtName));

            if (!cityOption) {
                console.warn('City not found for PIN territory:', territory);
                return;
            }

            citySelect.value = cityOption.value;

            loadAreas(cityOption.value).then(() => {
                const areaOption = areaId
                    ? Array.from(areaSelect.options).find(opt => opt.value === areaId)
                    : Array.from(areaSelect.options).find(opt => opt.text.trim().toLowerCase().includes(areaName));

                if (areaOption) {
                    areaSelect.value = areaOption.value;
                }
            });
        });
    });
}

window.showStatus = function(message, type = 'info') {
    const statusEl = document.getElementById('pin_status');
    let icon = 'fa-info-circle';
    let color = 'text-blue-600';
    
    switch(type) {
        case 'success':
            icon = 'fa-check-circle';
            color = 'text-green-600';
            break;
        case 'error':
            icon = 'fa-times-circle';
            color = 'text-red-600';
            break;
        case 'warning':
            icon = 'fa-exclamation-triangle';
            color = 'text-amber-600';
            break;
        case 'loading':
            icon = 'fa-spinner fa-spin';
            color = 'text-gray-600';
            break;
    }
    
    statusEl.innerHTML = `<span class="${color}"><i class="fas ${icon} mr-2"></i>${message}</span>`;
}

window.enableManualEntry = function() {
    const stateSelect = document.getElementById('state_id');
    const fallbackEl = document.getElementById('manual_fallback');
    showStatus('Please select your State below to continue', 'blue-600');
    fallbackEl?.classList.add('hidden');
    stateSelect.focus();
}

// ==================== CASCADING DROPDOWNS ====================
document.addEventListener('DOMContentLoaded', function() {
    setupStateDropdown();
    setupDistrictDropdown();
    setupCityDropdown();
    setupInputFormatting();

    const stateSelect = document.getElementById('state_id');
    const districtSelect = document.getElementById('district_id');
    const citySelect = document.getElementById('city_id');
    const areaSelect = document.getElementById('area_id');

    const oldStateId = '{{ old('state_id') }}';
    const oldDistrictId = '{{ old('district_id') }}';
    const oldCityId = '{{ old('city_id') }}';
    const oldAreaId = '{{ old('area_id') }}';

    if (oldStateId && stateSelect && String(stateSelect.value) === String(oldStateId)) {
        loadDistricts(oldStateId).then(() => {
            if (oldDistrictId && districtSelect) {
                districtSelect.value = String(oldDistrictId);
                return loadCities(oldDistrictId);
            }
            return Promise.resolve();
        }).then(() => {
            if (oldCityId && citySelect) {
                citySelect.value = String(oldCityId);
                return loadAreas(oldCityId);
            }
            return Promise.resolve();
        }).then(() => {
            if (oldAreaId && areaSelect) {
                areaSelect.value = String(oldAreaId);
            }
        });
    }
});

function setupStateDropdown() {
    const stateSelect = document.getElementById('state_id');
    if (!stateSelect) return;
    
    stateSelect.addEventListener('change', function() {
        const stateId = this.value;
        const districtSelect = document.getElementById('district_id');
        const citySelect = document.getElementById('city_id');
        const areaSelect = document.getElementById('area_id');
        
        // Reset dependent dropdowns
        districtSelect.innerHTML = '<option value="">Select District</option>';
        citySelect.innerHTML = '<option value="">Select City</option>';
        areaSelect.innerHTML = '<option value="">Select Locality / Area</option>';
        
        if (!stateId) return;
        
        // Load districts
        loadDistricts(stateId);
    });
}

function setupDistrictDropdown() {
    const districtSelect = document.getElementById('district_id');
    if (!districtSelect) return;
    
    districtSelect.addEventListener('change', function() {
        const districtId = this.value;
        const citySelect = document.getElementById('city_id');
        const areaSelect = document.getElementById('area_id');
        
        // Reset city & area dropdowns
        citySelect.innerHTML = '<option value="">Select City</option>';
        areaSelect.innerHTML = '<option value="">Select Locality / Area</option>';
        
        if (!districtId) return;
        
        // Load cities
        loadCities(districtId);
    });
}

function setupCityDropdown() {
    const citySelect = document.getElementById('city_id');
    if (!citySelect) return;
    
    citySelect.addEventListener('change', function() {
        const cityId = this.value;
        const areaSelect = document.getElementById('area_id');
        
        // Reset area dropdown
        areaSelect.innerHTML = '<option value="">Select Locality / Area</option>';
        
        if (!cityId) return;
        
        // Load areas
        loadAreas(cityId);
    });
}

function loadDistricts(stateId) {
    const districtSelect = document.getElementById('district_id');
    if (!districtSelect || !stateId) return Promise.resolve([]);

    districtSelect.innerHTML = '<option value="">Select District</option>';

    return fetch(getDistrictsUrl(stateId))
        .then(r => r.json())
        .then(data => {
            console.log('Districts loaded:', data);
            data.forEach(d => {
                const opt = document.createElement('option');
                opt.value = String(d.id);
                opt.text = d.name;
                districtSelect.appendChild(opt);
            });
            return data;
        })
        .catch(e => {
            console.error('District load failed:', e);
            return [];
        });
}

function loadCities(districtId) {
    const citySelect = document.getElementById('city_id');
    if (!citySelect || !districtId) return Promise.resolve([]);

    citySelect.innerHTML = '<option value="">Select City</option>';

    return fetch(getCitiesUrl(districtId))
        .then(r => r.json())
        .then(data => {
            console.log('Cities loaded:', data);
            data.forEach(c => {
                const opt = document.createElement('option');
                opt.value = String(c.id);
                opt.text = c.name;
                citySelect.appendChild(opt);
            });
            return data;
        })
        .catch(e => {
            console.error('City load failed:', e);
            return [];
        });
}

function loadAreas(cityId) {
    const areaSelect = document.getElementById('area_id');
    if (!areaSelect || !cityId) return Promise.resolve([]);

    areaSelect.innerHTML = '<option value="">Select Locality / Area</option>';

    return fetch(getAreasUrl(cityId))
        .then(r => r.json())
        .then(data => {
            console.log('Areas loaded:', data);
            data.forEach(a => {
                const opt = document.createElement('option');
                opt.value = String(a.id);
                opt.text = a.name;
                areaSelect.appendChild(opt);
            });
            return data;
        })
        .catch(e => {
            console.error('Area load failed:', e);
            return [];
        });
}

function setupInputFormatting() {
    // Phone number - 10 digits only
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '').slice(0, 10);
        });
    }
    
    // Alternate phone - 10 digits only
    const altPhoneInput = document.getElementById('alt_phone');
    if (altPhoneInput) {
        altPhoneInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '').slice(0, 10);
        });
    }
    
    // Aadhaar - 12 digits only
    const aadhaarInput = document.getElementById('aadhaar');
    if (aadhaarInput) {
        aadhaarInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '').slice(0, 12);
        });
    }
    
    // PIN code - 6 digits only
    const pincodeInput = document.getElementById('pincode');
    if (pincodeInput) {
        pincodeInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
        });
    }
    
    // PAN - 10 chars, uppercase
    const panInput = document.getElementById('pan_no');
    if (panInput) {
        panInput.addEventListener('input', function(e) {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 10);
        });
    }
}

window.previewImage = function(input, previewId) {
    const preview = document.getElementById(previewId);
    const label = document.getElementById(previewId.replace('_preview', '_label'));
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (label) label.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
