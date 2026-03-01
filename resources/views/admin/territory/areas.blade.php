@extends('layouts.app')

@section('title', 'Manage Areas')
@section('page-title', 'Territory Management / Areas')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Areas</h1>
        <button onclick="openModal('createModal')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">+ Add Area</button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">City</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">District</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($areas as $area)
                <tr>
                    <td class="px-6 py-4 font-mono text-sm">{{ $area->code }}</td>
                    <td class="px-6 py-4 font-medium">{{ $area->name }}</td>
                    <td class="px-6 py-4 text-sm">{{ $area->city?->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm">{{ $area->city?->district?->name ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded {{ $area->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $area->is_active ? 'Active' : 'Inactive' }}</span>
                    </td>
                    <td class="px-6 py-4 space-x-2">
                        <button onclick="editArea({{ $area->id }}, '{{ $area->code }}', '{{ $area->name }}', {{ $area->city?->district?->state_id ?? 'null' }}, {{ $area->city?->district_id ?? 'null' }}, {{ $area->city_id }}, '{{ $area->description ?? '' }}', {{ $area->is_active ? 1 : 0 }})" class="text-blue-600 hover:text-blue-800 text-sm">Edit</button>
                        <form action="{{ route('admin.territory.areas.destroy', $area) }}" method="POST" class="inline" onsubmit="return confirm('Delete this area?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">No areas found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $areas->links() }}</div>
</div>

<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <h2 class="text-xl font-bold mb-4">Add New Area</h2>
            <form action="{{ route('admin.territory.areas.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Area Code *</label>
                        <input type="text" name="code" required maxlength="10" class="w-full border rounded-lg px-3 py-2 uppercase" placeholder="e.g., AND-E">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Area Name *</label>
                        <input type="text" name="name" required maxlength="100" class="w-full border rounded-lg px-3 py-2" placeholder="e.g., Andheri East">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">State *</label>
                        <select id="create_state_id" class="w-full border rounded-lg px-3 py-2" onchange="loadDistricts(this.value, 'create_district_id')">
                            <option value="">-- Select State --</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">District *</label>
                        <select id="create_district_id" class="w-full border rounded-lg px-3 py-2" disabled onchange="loadCities(this.value, 'create_city_id')">
                            <option value="">-- Select State First --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                        <select name="city_id" id="create_city_id" required class="w-full border rounded-lg px-3 py-2" disabled>
                            <option value="">-- Select District First --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="2" class="w-full border rounded-lg px-3 py-2" placeholder="Optional description"></textarea>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" checked class="mr-2">
                        <label class="text-sm text-gray-700">Active</label>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <h2 class="text-xl font-bold mb-4">Edit Area</h2>
            <form id="editForm" method="POST">
                @csrf @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Area Code *</label>
                        <input type="text" name="code" id="edit_code" required maxlength="10" class="w-full border rounded-lg px-3 py-2 uppercase">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Area Name *</label>
                        <input type="text" name="name" id="edit_name" required maxlength="100" class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">State *</label>
                        <select id="edit_state_id" class="w-full border rounded-lg px-3 py-2" onchange="loadDistricts(this.value, 'edit_district_id')">
                            <option value="">-- Select State --</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">District *</label>
                        <select id="edit_district_id" class="w-full border rounded-lg px-3 py-2" onchange="loadCities(this.value, 'edit_city_id')">
                            <option value="">-- Select State First --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                        <select name="city_id" id="edit_city_id" required class="w-full border rounded-lg px-3 py-2">
                            <option value="">-- Select District First --</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="edit_description" rows="2" class="w-full border rounded-lg px-3 py-2"></textarea>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="mr-2">
                        <label class="text-sm text-gray-700">Active</label>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

function loadDistricts(stateId, targetSelectId, selectedId = null) {
    const select = document.getElementById(targetSelectId);
    select.innerHTML = '<option value="">Loading...</option>';
    select.disabled = true;
    if (!stateId) { select.innerHTML = '<option value="">-- Select State First --</option>'; return; }
    fetch(`/admin/territory/states/${stateId}/districts`)
        .then(r => r.json())
        .then(data => {
            select.innerHTML = '<option value="">-- Select District --</option>';
            data.forEach(d => { const opt = document.createElement('option'); opt.value = d.id; opt.textContent = d.name; if (selectedId && d.id == selectedId) opt.selected = true; select.appendChild(opt); });
            select.disabled = false;
        });
}

function loadCities(districtId, targetSelectId, selectedId = null) {
    const select = document.getElementById(targetSelectId);
    select.innerHTML = '<option value="">Loading...</option>';
    select.disabled = true;
    if (!districtId) { select.innerHTML = '<option value="">-- Select District First --</option>'; return; }
    fetch(`/admin/territory/districts/${districtId}/cities`)
        .then(r => r.json())
        .then(data => {
            select.innerHTML = '<option value="">-- Select City --</option>';
            data.forEach(c => { const opt = document.createElement('option'); opt.value = c.id; opt.textContent = c.name; if (selectedId && c.id == selectedId) opt.selected = true; select.appendChild(opt); });
            select.disabled = false;
        });
}

function editArea(id, code, name, stateId, districtId, cityId, description, isActive) {
    document.getElementById('editForm').action = `/admin/territory/areas/${id}`;
    document.getElementById('edit_code').value = code;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_description').value = description;
    document.getElementById('edit_state_id').value = stateId || '';
    document.getElementById('edit_is_active').checked = isActive === 1;
    if (stateId) {
        loadDistricts(stateId, 'edit_district_id', districtId);
        if (districtId) loadCities(districtId, 'edit_city_id', cityId);
    }
    openModal('editModal');
}

window.onclick = function(e) { if (e.target.classList.contains('fixed')) e.target.classList.add('hidden'); }
</script>
@endpush
@endsection