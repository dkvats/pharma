@extends('layouts.app')

@section('title', 'Bulk Sale Entry')

@section('content')
<div class="container mx-auto px-4 py-6">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Bulk Sale Entry</h1>
            <p class="text-sm text-gray-500 mt-1">Record multiple product sales from one prescription in a single submission.</p>
        </div>
        <a href="{{ route('store.stock.index') }}"
           class="text-sm text-gray-600 hover:text-gray-800 flex items-center gap-1">
            <i class="fas fa-arrow-left"></i> Back to Stock
        </a>
    </div>

    <!-- Flash messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($stocks->isEmpty())
        <!-- No stock available state -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-10 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-boxes text-gray-400 text-2xl"></i>
            </div>
            <p class="text-gray-600 font-medium">No stock available</p>
            <p class="text-sm text-gray-400 mt-1">Stock will appear here once orders are delivered to your store.</p>
        </div>
    @else
        <form action="{{ route('store.bulk-sale.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Products table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">
                        Products &amp; Quantities
                    </h2>
                    <span class="text-xs text-gray-400">Leave quantity blank or 0 to skip a product</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-8">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300"
                                           title="Select / deselect all" onchange="toggleAll(this)">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Product</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Price</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Available</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-40">Quantity to Sell</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100" id="productRows">
                            @foreach($stocks as $stock)
                                <tr class="product-row hover:bg-gray-50 transition-colors" data-max="{{ $stock->available_stock }}">
                                    <td class="px-6 py-4">
                                        {{-- Hidden product_id — sent when row is selected --}}
                                        <input type="checkbox" name="products[]" value="{{ $stock->product_id }}"
                                               class="row-checkbox rounded border-gray-300"
                                               onchange="toggleRow(this)">
                                        {{-- quantities[] must stay parallel to products[] --}}
                                        <input type="hidden" class="qty-hidden" name="quantities[]" value="0">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-pills text-blue-400 text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-800">{{ $stock->product->name ?? 'N/A' }}</p>
                                                <p class="text-xs text-gray-400">{{ $stock->product->sku ?? '' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($stock->product && $stock->product->mrp > $stock->product->price)
                                            <div class="flex flex-col items-end">
                                                <span class="text-xs text-gray-400 line-through">₹{{ number_format($stock->product->mrp, 2) }}</span>
                                                <span class="text-sm font-semibold text-green-600">₹{{ number_format($stock->product->price, 2) }}</span>
                                                <span class="text-xs text-red-500 font-medium">{{ $stock->product->discount_percentage }}% OFF</span>
                                            </div>
                                        @else
                                            <span class="text-sm font-semibold text-gray-700">₹{{ number_format($stock->product->price ?? 0, 2) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-sm font-semibold {{ $stock->available_stock > 5 ? 'text-green-600' : 'text-amber-600' }}">
                                            {{ $stock->available_stock }} units
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <input type="number"
                                               class="qty-input w-24 px-3 py-1.5 border border-gray-300 rounded-lg text-center text-sm
                                                      focus:outline-none focus:ring-2 focus:ring-blue-400 disabled:bg-gray-100 disabled:text-gray-400"
                                               min="1" max="{{ $stock->available_stock }}"
                                               placeholder="—"
                                               disabled
                                               oninput="syncQty(this)">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Doctor + Prescription section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">
                    Prescription Details
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Doctor Code or Phone
                            <span class="text-gray-400 font-normal">(Optional)</span>
                        </label>
                        <input type="text" name="doctor_code"
                               value="{{ old('doctor_code') }}"
                               placeholder="Enter doctor code or phone number"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500
                                      @error('doctor_code') border-red-500 @enderror">
                        @error('doctor_code')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-400 mt-1">
                            Enter the referring doctor's code or phone. Leave blank for store direct sale.
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Prescription <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="prescription"
                               accept=".jpg,.jpeg,.png,.pdf" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500
                                      @error('prescription') border-red-500 @enderror">
                        @error('prescription')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG or PDF — max 2 MB. One prescription covers all products in this batch.</p>
                    </div>
                </div>
            </div>

            <!-- Summary bar + Submit -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    Selected: <span id="selectedCount" class="font-bold text-gray-900">0</span> product(s) &nbsp;|&nbsp;
                    Total units: <span id="totalUnits" class="font-bold text-blue-700">0</span>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('store.stock.index') }}"
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-5 rounded-lg text-sm">
                        Cancel
                    </a>
                    <button type="submit" id="submitBtn" disabled
                            class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed
                                   text-white font-semibold py-2 px-6 rounded-lg text-sm transition-colors">
                        <i class="fas fa-save mr-1"></i> Save All Sales
                    </button>
                </div>
            </div>

        </form>
    @endif

</div>

<script>
(function () {
    /**
     * Keep the hidden qty-input in sync with the visible number input,
     * and update the summary bar.
     */
    function updateSummary() {
        var rows = document.querySelectorAll('.product-row');
        var selectedCount = 0;
        var totalUnits    = 0;

        rows.forEach(function (row) {
            var cb  = row.querySelector('.row-checkbox');
            var inp = row.querySelector('.qty-input');
            if (cb && cb.checked) {
                selectedCount++;
                totalUnits += (parseInt(inp.value) || 0);
            }
        });

        document.getElementById('selectedCount').textContent = selectedCount;
        document.getElementById('totalUnits').textContent    = totalUnits;
        document.getElementById('submitBtn').disabled        = (selectedCount === 0 || totalUnits === 0);
    }

    // When checkbox toggled: enable/disable the qty input
    window.toggleRow = function (cb) {
        var row     = cb.closest('.product-row');
        var qtyInp  = row.querySelector('.qty-input');
        var qtyHid  = row.querySelector('.qty-hidden');
        var maxQty  = parseInt(row.dataset.max) || 1;

        if (cb.checked) {
            qtyInp.disabled = false;
            if (!qtyInp.value || qtyInp.value === '0') {
                qtyInp.value = 1;
            }
            qtyHid.value = qtyInp.value;
        } else {
            qtyInp.disabled = true;
            qtyInp.value    = '';
            qtyHid.value    = 0;
        }
        updateSummary();
    };

    // Sync visible qty → hidden field
    window.syncQty = function (inp) {
        var row    = inp.closest('.product-row');
        var qtyHid = row.querySelector('.qty-hidden');
        var maxQty = parseInt(row.dataset.max) || 1;
        var val    = parseInt(inp.value) || 0;

        // Clamp to max available
        if (val > maxQty) {
            inp.value = maxQty;
            val       = maxQty;
        }
        qtyHid.value = val > 0 ? val : 0;
        updateSummary();
    };

    // Select-all toggle
    window.toggleAll = function (masterCb) {
        document.querySelectorAll('.row-checkbox').forEach(function (cb) {
            cb.checked = masterCb.checked;
            toggleRow(cb);
        });
    };

    // Initial summary run
    updateSummary();
})();
</script>
@endsection
