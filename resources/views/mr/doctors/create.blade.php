@extends('layouts.app')

@section('title', 'Register Doctor')
@section('page-title', 'Register New Doctor')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Doctor Registration Form</h3>
        </div>
        
        <form action="{{ route('mr.doctors.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <!-- Basic Information -->
            <div>
                <h4 class="text-md font-medium text-gray-700 mb-4">Basic Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Doctor Name *</label>
                        <input type="text" name="name" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror" value="{{ old('name') }}">
                        @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Specialization</label>
                        <input type="text" name="specialization" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" value="{{ old('specialization') }}">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Clinic/Hospital Name</label>
                        <input type="text" name="clinic_name" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" value="{{ old('clinic_name') }}">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                        <input type="text" name="mobile" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" value="{{ old('mobile') }}">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" value="{{ old('email') }}">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" name="phone" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" value="{{ old('phone') }}">
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <textarea name="address" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('address') }}</textarea>
                </div>
            </div>
            
            <!-- Location -->
            <div>
                <h4 class="text-md font-medium text-gray-700 mb-4">Location</h4>
                
                <!-- PIN Code Auto-fill -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <label class="block text-sm font-medium text-blue-800 mb-1">PIN Code (Auto-fill Location)</label>
                    <div class="flex gap-2">
                        <input type="text" id="pin_code" maxlength="6" placeholder="Enter 6-digit PIN"
                            class="flex-1 px-4 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            oninput="lookupPinCode(this.value)">
                        <button type="button" onclick="lookupPinCode(document.getElementById('pin_code').value)"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Lookup
                        </button>
                    </div>
                    <p id="pin_status" class="text-sm mt-2"></p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">State *</label>
                        <select name="state_id" id="state_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Select State</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}" data-name="{{ $state->name }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">District *</label>
                        <select name="district_id" id="district_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Select District</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                        <select name="city_id" id="city_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Select City</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Locality / Post Office *</label>
                        <select name="area_id" id="area_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Locality / Post Office</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">PIN codes map to post offices (B.O/S.O/H.O)</p>
                    </div>
                </div>
            </div>
            
            <!-- KYC Details -->
            <div>
                <h4 class="text-md font-medium text-gray-700 mb-4">KYC Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Aadhaar Number</label>
                        <input type="text" name="aadhaar_no" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" value="{{ old('aadhaar_no') }}">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PAN Number</label>
                        <input type="text" name="pan_no" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" value="{{ old('pan_no') }}">
                    </div>
                </div>
            </div>
            
            <!-- Bank Details -->
            <div>
                <h4 class="text-md font-medium text-gray-700 mb-4">Bank Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                        <input type="text" name="bank_name" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" value="{{ old('bank_name') }}">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">IFSC Code</label>
                        <input type="text" name="ifsc" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" value="{{ old('ifsc') }}">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                        <input type="text" name="account_no" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" value="{{ old('account_no') }}">
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-4 pt-4 border-t">
                <a href="{{ route('mr.doctors.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Register Doctor</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// PIN Code lookup via API
function lookupPinCode(pin) {
    const statusEl = document.getElementById('pin_status');
    
    // Trim PIN input
    pin = (pin || '').trim();
    
    // Validate PIN (6 digits)
    if (!pin || pin.length !== 6 || !/^\d{6}$/.test(pin)) {
        statusEl.innerHTML = '<span class="text-orange-600">Please enter a valid 6-digit PIN code</span>';
        return;
    }
    
    statusEl.innerHTML = '<span class="text-blue-600">Looking up...</span>';
    
    // Call API (public endpoint - no role restriction)
    fetch(`/api/pincode/${pin}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(result => {
            if (result.success && result.data) {
                const location = result.data;
                const territory = location.territory;
                
                if (territory) {
                    // Territory auto-synced from PIN - auto-fill all dropdowns
                    statusEl.innerHTML = '<span class="text-green-600">✓ Location found: ' + location.post_office + ', ' + location.state + '</span>';
                    
                    // Auto-fill state
                    addOptionIfNotExists('state_id', territory.state_id, location.state);
                    document.getElementById('state_id').value = territory.state_id;
                    
                    // Load districts and auto-fill
                    loadDistrictsAndSelect(territory.state_id, territory.district_id, location.district);
                    
                    // Store remaining IDs for cascading fill
                    window.pendingTerritory = {
                        cityId: territory.city_id,
                        areaId: territory.area_id,
                        postOffice: location.post_office
                    };
                } else {
                    // PIN found but territory sync failed
                    statusEl.innerHTML = '<span class="text-orange-600">⚠ Location found, but territory sync failed. Please select manually.</span>';
                }
            } else {
                // Show backend error message or default
                const message = result.message || 'PIN code not found';
                statusEl.innerHTML = '<span class="text-red-600">✗ ' + message + '. Please enter location manually.</span>';
            }
        })
        .catch(error => {
            console.error('PIN lookup error:', error);
            statusEl.innerHTML = '<span class="text-orange-600">⚠ Unable to lookup PIN. Please enter location manually.</span>';
        });
}

// Check if an option with given text exists in dropdown
function optionExistsByText(selectId, text) {
    const select = document.getElementById(selectId);
    if (!select || !text) return false;
    
    const searchText = text.trim().toLowerCase();
    
    for (let option of select.options) {
        const optionText = option.text.trim().toLowerCase();
        // Skip the default "Select..." option
        if (option.value === '') continue;
        
        // Try exact match first, then contains match
        if (optionText === searchText || optionText.includes(searchText) || searchText.includes(optionText)) {
            return true;
        }
    }
    
    return false;
}

// Select dropdown option by matching text content
function selectByText(selectId, text) {
    const select = document.getElementById(selectId);
    if (!select || !text) return false;
    
    const searchText = text.trim().toLowerCase();
    
    for (let option of select.options) {
        const optionText = option.text.trim().toLowerCase();
        // Try exact match first, then contains match
        if (optionText === searchText || optionText.includes(searchText) || searchText.includes(optionText)) {
            option.selected = true;
            select.dispatchEvent(new Event('change'));
            return true;
        }
    }
    
    return false;
}

// Try to match and select area from post office name
function autoFillAreaFromPostOffice(postOffice) {
    const areaSelect = document.getElementById('area_id');
    if (!areaSelect || areaSelect.options.length <= 1 || !postOffice) return false;
    
    // Clean up post office name (remove S.O, H.O, etc.)
    const cleanPostOffice = postOffice.replace(/\s+(s\.o|h\.o|b\.o|so|ho)\.?$/i, '').trim();
    
    return selectByText('area_id', cleanPostOffice) || selectByText('area_id', postOffice);
}

// Clean post office name for display (remove B.O, S.O, H.O suffixes)
function cleanPostOfficeName(name) {
    if (!name) return name;
    // Remove B.O, S.O, H.O, B.O, etc. at the end
    return name.replace(/\s+(B\.O|S\.O|H\.O|SO|HO|BO)\.?$/i, '').trim();
}

// Add option to dropdown if it doesn't exist
function addOptionIfNotExists(selectId, value, text, cleanDisplay = false) {
    const select = document.getElementById(selectId);
    if (!select) return;
    
    // Check if option already exists
    let exists = false;
    for (let option of select.options) {
        if (option.value == value) {
            exists = true;
            break;
        }
    }
    
    // Add if not exists
    if (!exists) {
        const option = document.createElement('option');
        option.value = value;
        // Clean display text for post office names (remove B.O/S.O/H.O)
        option.textContent = cleanDisplay ? cleanPostOfficeName(text) : text;
        // Store original name as data attribute for reference
        option.dataset.originalName = text;
        select.appendChild(option);
    }
}

// Load districts for a state and select specific district
function loadDistrictsAndSelect(stateId, districtId, districtName) {
    const districtSelect = document.getElementById('district_id');
    const citySelect = document.getElementById('city_id');
    const areaSelect = document.getElementById('area_id');
    
    districtSelect.innerHTML = '<option value="">Select District</option>';
    citySelect.innerHTML = '<option value="">Select City</option>';
    areaSelect.innerHTML = '<option value="">Select Locality / Post Office</option>';
    
    if (stateId) {
        fetch(`/mr/doctors/get-districts/${stateId}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(district => {
                    districtSelect.innerHTML += `<option value="${district.id}">${district.name}</option>`;
                });
                
                // Add and select the target district
                if (districtId) {
                    addOptionIfNotExists('district_id', districtId, districtName);
                    districtSelect.value = districtId;
                    
                    // Trigger change and load cities
                    loadCitiesAndSelect(districtId);
                }
            });
    }
}

// Load cities for a district and select specific city
function loadCitiesAndSelect(districtId) {
    const citySelect = document.getElementById('city_id');
    const areaSelect = document.getElementById('area_id');
    
    citySelect.innerHTML = '<option value="">Select City</option>';
    areaSelect.innerHTML = '<option value="">Select Locality / Post Office</option>';
    
    if (districtId && window.pendingTerritory) {
        fetch(`/mr/doctors/get-cities/${districtId}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(city => {
                    citySelect.innerHTML += `<option value="${city.id}">${city.name}</option>`;
                });
                
                // Add and select the target city
                const cityId = window.pendingTerritory.cityId;
                if (cityId) {
                    // Get city name from pending territory or use district name
                    const cityName = data.find(c => c.id == cityId)?.name || window.pendingTerritory.postOffice;
                    addOptionIfNotExists('city_id', cityId, cityName);
                    citySelect.value = cityId;
                    
                    // Trigger change and load areas
                    loadAreasAndSelect(cityId);
                }
            });
    }
}

// Load areas for a city and select specific area
function loadAreasAndSelect(cityId) {
    const areaSelect = document.getElementById('area_id');
    
    areaSelect.innerHTML = '<option value="">Select Locality / Post Office</option>';
    
    if (cityId && window.pendingTerritory) {
        fetch(`/mr/doctors/get-areas/${cityId}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(area => {
                    // Clean display name for existing areas too
                    const displayName = cleanPostOfficeName(area.name);
                    areaSelect.innerHTML += `<option value="${area.id}" data-original-name="${area.name}">${displayName}</option>`;
                });
                
                // Add and select the target area (with cleaned display name)
                const areaId = window.pendingTerritory.areaId;
                const postOffice = window.pendingTerritory.postOffice;
                if (areaId) {
                    addOptionIfNotExists('area_id', areaId, postOffice, true); // true = clean display
                    areaSelect.value = areaId;
                }
                
                // Clear pending territory
                window.pendingTerritory = null;
            });
    }
}

// Cascading dropdowns for location
const stateSelect = document.getElementById('state_id');
const districtSelect = document.getElementById('district_id');
const citySelect = document.getElementById('city_id');
const areaSelect = document.getElementById('area_id');

stateSelect.addEventListener('change', function() {
    const stateId = this.value;
    districtSelect.innerHTML = '<option value="">Select District</option>';
    citySelect.innerHTML = '<option value="">Select City</option>';
    areaSelect.innerHTML = '<option value="">Select Locality / Post Office</option>';
    
    if (stateId) {
        fetch(`/mr/doctors/get-districts/${stateId}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(district => {
                    districtSelect.innerHTML += `<option value="${district.id}">${district.name}</option>`;
                });
            });
    }
});

districtSelect.addEventListener('change', function() {
    const districtId = this.value;
    citySelect.innerHTML = '<option value="">Select City</option>';
    areaSelect.innerHTML = '<option value="">Select Locality / Post Office</option>';
    
    if (districtId) {
        fetch(`/mr/doctors/get-cities/${districtId}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(city => {
                    citySelect.innerHTML += `<option value="${city.id}">${city.name}</option>`;
                });
            });
    }
});

citySelect.addEventListener('change', function() {
    const cityId = this.value;
    areaSelect.innerHTML = '<option value="">Select Locality / Post Office</option>';
    
    if (cityId) {
        fetch(`/mr/doctors/get-areas/${cityId}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(area => {
                    // Clean display name (remove B.O/S.O/H.O)
                    const displayName = cleanPostOfficeName(area.name);
                    areaSelect.innerHTML += `<option value="${area.id}" data-original-name="${area.name}">${displayName}</option>`;
                });
                
                // Try to auto-fill area if we have pending post office from PIN lookup
                if (window.pendingPostOffice) {
                    setTimeout(() => {
                        if (autoFillAreaFromPostOffice(window.pendingPostOffice)) {
                            window.pendingPostOffice = null; // Clear after successful match
                        }
                    }, 100);
                }
            });
    }
});
</script>
@endpush
@endsection
