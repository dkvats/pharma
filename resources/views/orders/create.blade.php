@extends('layouts.app')

@section('title', 'Place New Order')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Place New Order</h1>
            <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-800">
                &larr; Back to Orders
            </a>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->has('quantity'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ $errors->first('quantity') }}
            </div>
        @endif

        <form id="orderForm" action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Products Section -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Select Products</h2>
                
                <div id="productItems">
                    <!-- Product rows will be added here -->
                </div>

                <button type="button" onclick="addProductRow()" class="mt-4 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg">
                    + Add Product
                </button>

                @error('items')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Active Offers -->
            @if($dailyOffer || $ongoingOffers->count() > 0)
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Available Offers</h2>
                
                @if($dailyOffer)
                <div class="bg-gradient-to-r from-orange-500 to-red-500 text-white p-4 rounded-lg mb-4">
                    <span class="inline-block px-2 py-1 bg-white/20 rounded text-xs mb-2">Offer of the Day</span>
                    <h3 class="font-bold">{{ $dailyOffer->title }}</h3>
                    <p class="text-sm">{{ $dailyOffer->description }}</p>
                    <p class="font-bold mt-2">{{ $dailyOffer->discount_display }}</p>
                    <label class="flex items-center mt-2 cursor-pointer">
                        <input type="radio" name="offer_id" value="{{ $dailyOffer->id }}" class="mr-2" onchange="calculateTotal()">
                        <span class="text-sm">Apply this offer</span>
                    </label>
                </div>
                @endif

                @if($ongoingOffers->count() > 0)
                <div class="space-y-2">
                    <h3 class="font-medium text-gray-700">Ongoing Offers</h3>
                    @foreach($ongoingOffers as $offer)
                    <div class="border border-gray-200 p-3 rounded-lg flex justify-between items-center">
                        <div>
                            <p class="font-medium">{{ $offer->title }}</p>
                            <p class="text-sm text-gray-500">{{ $offer->discount_display }}</p>
                        </div>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="offer_id" value="{{ $offer->id }}" class="mr-2" onchange="calculateTotal()">
                            <span class="text-sm">Apply</span>
                        </label>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endif

            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Summary</h2>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Subtotal:</span>
                        <span id="subtotalAmount">₹0.00</span>
                    </div>
                    <div class="flex justify-between items-center text-green-600" id="discountRow" style="display: none;">
                        <span>Discount:</span>
                        <span id="discountAmount">-₹0.00</span>
                    </div>
                    <div class="flex justify-between items-center text-xl font-bold border-t pt-2">
                        <span>Total Amount:</span>
                        <span id="totalAmount">₹0.00</span>
                    </div>
                </div>
            </div>

            <!-- Referral Code -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Referral (Optional)</h2>
                <div class="mb-4">
                    <label for="referral_code" class="block text-sm font-medium text-gray-700 mb-1">Referral Code</label>
                    <input type="text" id="referral_code" name="referral_code" value="{{ old('referral_code') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter doctor or store code">
                    <p class="text-gray-500 text-sm mt-1">If you have a referral code from a doctor or store, enter it here.</p>
                    @error('referral_code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @if(auth()->user()->hasRole('MR'))
            <!-- MR Doctor Selection -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Attribute to Doctor</h2>
                <div class="mb-4">
                    <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-1">Select Doctor</label>
                    <select id="doctor_id" name="doctor_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Select Doctor (Optional) --</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                {{ $doctor->name }} ({{ $doctor->unique_code }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-gray-500 text-sm mt-1">Select a doctor to attribute this sale to their account.</p>
                    @error('doctor_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            @endif

            @if(!auth()->user()->hasRole('Doctor') && !auth()->user()->hasRole('Store'))
            <!-- Prescription Upload - Only for End Users -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Prescription</h2>
                <div class="mb-4">
                    <label for="prescription" class="block text-sm font-medium text-gray-700 mb-1">Upload Prescription</label>
                    <input type="file" id="prescription" name="prescription" accept=".jpg,.jpeg,.png,.pdf"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('prescription') border-red-500 @enderror">
                    <p class="text-gray-500 text-sm mt-1">Upload a prescription if required. Accepted: JPG, PNG, PDF (max 4MB)</p>
                    @error('prescription')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            @endif

            <!-- Notes -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Additional Notes</h2>
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Any special instructions...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('dashboard') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg">
                    Place Order
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const products = @json($products);
    const offers = @json($ongoingOffers);
    const dailyOffer = @json($dailyOffer);
    let rowCount = 0;

    function addProductRow() {
        rowCount++;
        const container = document.getElementById('productItems');
        
        const row = document.createElement('div');
        row.className = 'product-row flex flex-col md:flex-row gap-4 mb-4 p-4 bg-gray-50 rounded-lg';
        row.innerHTML = `
            <div class="flex-1 w-full md:w-auto">
                <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                <select name="items[${rowCount}][product_id]" required onchange="updatePrice(${rowCount})"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 product-select">
                    <option value="">Select Product</option>
                    ${products.map(p => `<option value="${p.id}" data-price="${p.price}" data-stock="${p.stock}">${p.name} - ₹${p.price} (Stock: ${p.stock})</option>`).join('')}
                </select>
            </div>
            <div class="w-full md:w-32">
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                <input type="number" name="items[${rowCount}][quantity]" min="1" value="1" required oninput="calculateTotal()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 quantity-input">
            </div>
            <div class="w-full md:w-32">
                <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal</label>
                <div class="text-lg font-semibold text-gray-900 pt-2 subtotal">₹0.00</div>
            </div>
            <div class="flex items-end w-full md:w-auto">
                <button type="button" onclick="removeProductRow(this)" class="text-red-600 hover:text-red-800 font-semibold py-2 w-full md:w-auto">
                    Remove
                </button>
            </div>
        `;
        
        container.appendChild(row);
    }

    function removeProductRow(button) {
        button.closest('.product-row').remove();
        calculateTotal();
    }

    function updatePrice(rowId) {
        calculateTotal();
    }

    function getSelectedOffer() {
        const selectedOffer = document.querySelector('input[name="offer_id"]:checked');
        if (!selectedOffer) return null;
        
        const offerId = parseInt(selectedOffer.value);
        if (dailyOffer && dailyOffer.id === offerId) return dailyOffer;
        return offers.find(o => o.id === offerId);
    }

    function calculateTotal() {
        let subtotal = 0;
        document.querySelectorAll('.product-row').forEach(row => {
            const select = row.querySelector('.product-select');
            const quantity = row.querySelector('.quantity-input').value;
            const option = select.options[select.selectedIndex];
            
            if (option && option.value) {
                const price = parseFloat(option.dataset.price);
                const itemSubtotal = price * quantity;
                row.querySelector('.subtotal').textContent = '₹' + itemSubtotal.toFixed(2);
                subtotal += itemSubtotal;
            }
        });
        
        // Calculate discount
        let discount = 0;
        const selectedOffer = getSelectedOffer();
        if (selectedOffer && subtotal > 0) {
            if (selectedOffer.discount_type === 'percentage') {
                discount = subtotal * (selectedOffer.discount_value / 100);
            } else {
                discount = selectedOffer.discount_value;
            }
            // Cap discount at subtotal
            discount = Math.min(discount, subtotal);
        }
        
        const total = subtotal - discount;
        
        // Update display
        document.getElementById('subtotalAmount').textContent = '₹' + subtotal.toFixed(2);
        document.getElementById('totalAmount').textContent = '₹' + total.toFixed(2);
        
        // Show/hide discount row
        const discountRow = document.getElementById('discountRow');
        if (discount > 0) {
            discountRow.style.display = 'flex';
            document.getElementById('discountAmount').textContent = '-₹' + discount.toFixed(2);
        } else {
            discountRow.style.display = 'none';
        }
    }

    // Add first product row by default
    addProductRow();
</script>
@endsection
