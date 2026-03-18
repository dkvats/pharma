@extends('layouts.app')

@section('title', 'Edit Store')
@section('page-title', 'Edit Store')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6">
    <div class="mb-8 flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Store</h1>
            <p class="text-gray-600 mt-2">Update registered store details and resubmit for review.</p>
        </div>
        <a href="{{ route('mr.stores.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back to Store Module
        </a>
    </div>

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

    <form action="{{ route('mr.stores.update', $store) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Basic Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="store_name" class="block text-sm font-medium text-gray-700 mb-2">Store Name <span class="text-red-500">*</span></label>
                    <input type="text" id="store_name" name="store_name" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('store_name') border-red-500 @enderror"
                           value="{{ old('store_name', $store->store_name) }}">
                    @error('store_name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="owner_name" class="block text-sm font-medium text-gray-700 mb-2">Owner Name <span class="text-red-500">*</span></label>
                    <input type="text" id="owner_name" name="owner_name" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('owner_name') border-red-500 @enderror"
                           value="{{ old('owner_name', $store->owner_name) }}">
                    @error('owner_name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Mobile Number <span class="text-red-500">*</span></label>
                    <input type="tel" id="phone" name="phone" required maxlength="10"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                           inputmode="numeric" value="{{ old('phone', $store->phone) }}">
                    @error('phone')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                           value="{{ old('email', $store->email) }}">
                    @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Address & Location</h2>

            <div class="space-y-6">
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address <span class="text-red-500">*</span></label>
                    <textarea id="address" name="address" rows="3" required
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-500 @enderror">{{ old('address', $store->address) }}</textarea>
                    @error('address')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="pincode" class="block text-sm font-medium text-gray-700 mb-2">PIN Code <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <input type="text" id="pincode" name="pincode" maxlength="6" required
                               class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('pincode') border-red-500 @enderror"
                               inputmode="numeric" value="{{ old('pincode', $store->pincode) }}">
                        <button type="button" onclick="lookupPinCode(document.getElementById('pincode').value)"
                                class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                            <i class="fas fa-search mr-2"></i>Lookup
                        </button>
                    </div>
                    <div id="pin_status" class="text-sm mt-3"></div>
                    @error('pincode')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="state_id" class="block text-sm font-medium text-gray-700 mb-2">State <span class="text-red-500">*</span></label>
                        <select id="state_id" name="state_id" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('state_id') border-red-500 @enderror">
                            <option value="">Select State</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}" {{ (string) old('state_id', $store->state_id) === (string) $state->id ? 'selected' : '' }}>
                                    {{ $state->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('state_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="district_id" class="block text-sm font-medium text-gray-700 mb-2">District <span class="text-red-500">*</span></label>
                        <select id="district_id" name="district_id" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('district_id') border-red-500 @enderror">
                            <option value="">Select District</option>
                        </select>
                        @error('district_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="city_id" class="block text-sm font-medium text-gray-700 mb-2">City <span class="text-red-500">*</span></label>
                        <select id="city_id" name="city_id" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('city_id') border-red-500 @enderror">
                            <option value="">Select City</option>
                        </select>
                        @error('city_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="area_id" class="block text-sm font-medium text-gray-700 mb-2">Locality / Area <span class="text-red-500">*</span></label>
                        <select id="area_id" name="area_id" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('area_id') border-red-500 @enderror">
                            <option value="">Select Locality / Area</option>
                        </select>
                        @error('area_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('mr.stores.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">Cancel</a>
            <button type="submit" class="px-8 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                <i class="fas fa-save mr-2"></i>Update Store
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
window.getPincodeUrl = function(pin) {
    const baseUrl = '{{ route("api.pincode.lookup", ["pin" => "PIN_TEMP"]) }}';
    return baseUrl.replace('PIN_TEMP', pin);
};

window.getDistrictsUrl = function(stateId) {
    const baseUrl = '{{ route("mr.stores.get-districts", ["stateId" => "STATE_TEMP"]) }}';
    return baseUrl.replace('STATE_TEMP', stateId);
};

window.getCitiesUrl = function(districtId) {
    const baseUrl = '{{ route("mr.stores.get-cities", ["districtId" => "DIST_TEMP"]) }}';
    return baseUrl.replace('DIST_TEMP', districtId);
};

window.getAreasUrl = function(cityId) {
    const baseUrl = '{{ route("mr.stores.get-areas", ["cityId" => "CITY_TEMP"]) }}';
    return baseUrl.replace('CITY_TEMP', cityId);
};

window.showStatus = function(message, type = 'info') {
    const statusEl = document.getElementById('pin_status');
    if (!statusEl) return;

    let icon = 'fa-info-circle';
    let color = 'text-blue-600';

    if (type === 'success') {
        icon = 'fa-check-circle';
        color = 'text-green-600';
    } else if (type === 'error') {
        icon = 'fa-times-circle';
        color = 'text-red-600';
    } else if (type === 'loading') {
        icon = 'fa-spinner fa-spin';
        color = 'text-gray-600';
    }

    statusEl.innerHTML = `<span class="${color}"><i class="fas ${icon} mr-2"></i>${message}</span>`;
};

function loadDistricts(stateId) {
    const districtSelect = document.getElementById('district_id');
    if (!districtSelect || !stateId) return Promise.resolve([]);

    districtSelect.innerHTML = '<option value="">Select District</option>';

    return fetch(getDistrictsUrl(stateId))
        .then(r => r.json())
        .then(data => {
            data.forEach(d => {
                const opt = document.createElement('option');
                opt.value = String(d.id);
                opt.text = d.name;
                districtSelect.appendChild(opt);
            });
            return data;
        })
        .catch(() => []);
}

function loadCities(districtId) {
    const citySelect = document.getElementById('city_id');
    if (!citySelect || !districtId) return Promise.resolve([]);

    citySelect.innerHTML = '<option value="">Select City</option>';

    return fetch(getCitiesUrl(districtId))
        .then(r => r.json())
        .then(data => {
            data.forEach(c => {
                const opt = document.createElement('option');
                opt.value = String(c.id);
                opt.text = c.name;
                citySelect.appendChild(opt);
            });
            return data;
        })
        .catch(() => []);
}

function loadAreas(cityId) {
    const areaSelect = document.getElementById('area_id');
    if (!areaSelect || !cityId) return Promise.resolve([]);

    areaSelect.innerHTML = '<option value="">Select Locality / Area</option>';

    return fetch(getAreasUrl(cityId))
        .then(r => r.json())
        .then(data => {
            data.forEach(a => {
                const opt = document.createElement('option');
                opt.value = String(a.id);
                opt.text = a.name;
                areaSelect.appendChild(opt);
            });
            return data;
        })
        .catch(() => []);
}

window.lookupPinCode = function(pinValue) {
    const pin = (pinValue || '').trim();

    if (!pin || pin.length !== 6 || !/^\d{6}$/.test(pin)) {
        window.showStatus('Please enter a valid 6-digit PIN code', 'error');
        return;
    }

    window.showStatus('Looking up PIN code...', 'loading');

    fetch(window.getPincodeUrl(pin))
        .then(r => r.json())
        .then(data => {
            if (!(data.success && data.data)) {
                window.showStatus('PIN code not found.', 'error');
                return;
            }

            window.showStatus('✓ PIN found! Auto-filling location...', 'success');

            const pinData = data.data;
            const territory = pinData.territory || {};

            const stateSelect = document.getElementById('state_id');
            const districtSelect = document.getElementById('district_id');
            const citySelect = document.getElementById('city_id');
            const areaSelect = document.getElementById('area_id');

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

            if (!stateOption) return;

            stateSelect.value = stateOption.value;

            loadDistricts(stateOption.value).then(() => {
                const districtOption = districtId
                    ? Array.from(districtSelect.options).find(opt => opt.value === districtId)
                    : Array.from(districtSelect.options).find(opt => opt.text.trim().toLowerCase() === districtName);

                if (!districtOption) return;

                districtSelect.value = districtOption.value;

                loadCities(districtOption.value).then(() => {
                    const cityOption = cityId
                        ? Array.from(citySelect.options).find(opt => opt.value === cityId)
                        : Array.from(citySelect.options).find(opt => opt.text.trim().toLowerCase().includes(districtName));

                    if (!cityOption) return;

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
        })
        .catch(() => window.showStatus('Error looking up PIN code.', 'error'));
};

document.addEventListener('DOMContentLoaded', function() {
    const stateSelect = document.getElementById('state_id');
    const districtSelect = document.getElementById('district_id');
    const citySelect = document.getElementById('city_id');

    const initialDistrictId = '{{ old('district_id', $store->district_id) }}';
    const initialCityId = '{{ old('city_id', $store->city_id) }}';
    const initialAreaId = '{{ old('area_id', $store->area_id) }}';

    stateSelect.addEventListener('change', function() {
        loadDistricts(this.value);
        document.getElementById('city_id').innerHTML = '<option value="">Select City</option>';
        document.getElementById('area_id').innerHTML = '<option value="">Select Locality / Area</option>';
    });

    districtSelect.addEventListener('change', function() {
        loadCities(this.value);
        document.getElementById('area_id').innerHTML = '<option value="">Select Locality / Area</option>';
    });

    citySelect.addEventListener('change', function() {
        loadAreas(this.value);
    });

    const stateId = stateSelect.value;
    if (stateId) {
        loadDistricts(stateId).then(() => {
            if (initialDistrictId) {
                districtSelect.value = String(initialDistrictId);
                return loadCities(initialDistrictId);
            }
            return Promise.resolve();
        }).then(() => {
            if (initialCityId) {
                citySelect.value = String(initialCityId);
                return loadAreas(initialCityId);
            }
            return Promise.resolve();
        }).then(() => {
            if (initialAreaId) {
                document.getElementById('area_id').value = String(initialAreaId);
            }
        });
    }

    const phoneInput = document.getElementById('phone');
    const pincodeInput = document.getElementById('pincode');

    phoneInput?.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 10);
    });

    pincodeInput?.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
    });
});
</script>
@endpush
@endsection
