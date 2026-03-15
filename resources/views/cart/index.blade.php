@extends('layouts.app')

@section('title', 'My Cart')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">My Cart</h1>
        <a href="{{ route('products.catalog') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Continue Shopping
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(!$cart || $cart->items->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Your cart is empty</h3>
            <p class="mt-1 text-sm text-gray-500">Start adding products to your cart.</p>
            <div class="mt-6">
                <a href="{{ route('orders.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Browse Products
                </a>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($cart->items as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($item->product->image)
                                            <img class="h-10 w-10 rounded object-cover" src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded bg-gray-200 flex items-center justify-center">
                                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $item->product->sku ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">₹{{ number_format($item->product->price, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center space-x-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" class="w-16 px-2 py-1 border rounded text-center">
                                        <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm">Update</button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">₹{{ number_format($item->subtotal, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <form action="{{ route('cart.remove', $item) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right font-medium text-gray-900">Total:</td>
                            <td class="px-6 py-4 font-bold text-lg text-gray-900">₹{{ number_format($cart->total, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Available Offers (End User Only) -->
            @if(auth()->user()->hasRole('End User'))
                @php
                    $dailyOffer = \App\Models\Offer::active()->forUsers()->where('offer_type', 'daily')->first();
                    $ongoingOffers = \App\Models\Offer::active()->forUsers()->where('offer_type', 'ongoing')->get();
                @endphp
                @if($dailyOffer || $ongoingOffers->count() > 0)
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-purple-50 border-t border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Available Offers</h3>
                    <div class="space-y-2">
                        @if($dailyOffer)
                        <label class="flex items-center p-3 bg-white rounded-lg border border-orange-200 cursor-pointer hover:bg-orange-50 transition">
                            <input type="radio" name="selected_offer_id" value="{{ $dailyOffer->id }}" form="checkout-form" class="mr-3" checked onchange="updateCartTotal()">
                            <div class="flex-1">
                                <span class="inline-block px-2 py-0.5 bg-orange-500 text-white text-xs rounded">Offer of the Day</span>
                                <p class="font-medium text-gray-800">{{ $dailyOffer->title }}</p>
                                <p class="text-sm text-orange-600 font-semibold">{{ $dailyOffer->discount_display }}</p>
                            </div>
                        </label>
                        @endif
                        @foreach($ongoingOffers as $index => $offer)
                        <label class="flex items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="selected_offer_id" value="{{ $offer->id }}" form="checkout-form" class="mr-3" {{ !$dailyOffer && $index == 0 ? 'checked' : '' }} onchange="updateCartTotal()">
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">{{ $offer->title }}</p>
                                <p class="text-sm text-green-600 font-semibold">{{ $offer->discount_display }}</p>
                            </div>
                        </label>
                        @endforeach
                        <label class="flex items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="selected_offer_id" value="" form="checkout-form" class="mr-3" onchange="updateCartTotal()">
                            <span class="text-gray-600">No offer</span>
                        </label>
                    </div>
                </div>
                @endif
            @endif

            <!-- Order Summary -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span id="cart-subtotal" class="font-medium">₹{{ number_format($cart->total, 2) }}</span>
                    </div>
                    <div id="discount-row" class="flex justify-between text-green-600 hidden">
                        <span>Offer Discount:</span>
                        <span id="cart-discount" class="font-medium">-₹0.00</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t pt-2">
                        <span>Final Total:</span>
                        <span id="cart-final-total">₹{{ number_format($cart->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                <form action="{{ route('cart.clear') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Clear Cart</button>
                </form>
                
                <div class="flex space-x-4">
                    <a href="{{ route('products.catalog') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Continue Shopping
                    </a>
                    <form id="checkout-form" action="{{ route('orders.store') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="from_cart" value="1">
                        {{-- Blade-rendered offer_id: pre-selected offer sent directly to backend --}}
                        @if(auth()->user()->hasRole('End User'))
                            @php
                                $selectedOffer = $dailyOffer ?? ($ongoingOffers->first() ?? null);
                            @endphp
                            <input type="hidden" id="offer_id_input" name="offer_id" value="{{ $selectedOffer ? $selectedOffer->id : '' }}">
                        @else
                            <input type="hidden" id="offer_id_input" name="offer_id" value="">
                        @endif
                        <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 font-medium">
                            Proceed to Checkout
                        </button>
                    </form>
                </div>
            </div>

            @if(auth()->user()->hasRole('End User'))
            <script>
                const cartTotal = {{ $cart->total }};
                const offers = @json($ongoingOffers ?? collect());
                const dailyOffer = @json($dailyOffer ?? null);
                
                function updateCartTotal() {
                    const selectedOfferId = document.querySelector('input[name="selected_offer_id"]:checked')?.value;
                    const offerIdInput = document.getElementById('offer_id_input');
                    
                    // Debug: Log the selected offer
                    console.log('Selected Offer ID:', selectedOfferId);
                    
                    let discount = 0;
                    let selectedOffer = null;
                    
                    if (selectedOfferId) {
                        offerIdInput.value = selectedOfferId;
                        if (dailyOffer && dailyOffer.id == selectedOfferId) {
                            selectedOffer = dailyOffer;
                        } else {
                            selectedOffer = offers.find(o => o.id == selectedOfferId);
                        }
                        
                        if (selectedOffer) {
                            if (selectedOffer.discount_type === 'percentage') {
                                discount = cartTotal * (selectedOffer.discount_value / 100);
                            } else {
                                discount = selectedOffer.discount_value;
                            }
                            discount = Math.min(discount, cartTotal);
                        }
                    } else {
                        offerIdInput.value = '';
                    }
                    
                    const finalTotal = cartTotal - discount;
                    
                    document.getElementById('cart-discount').textContent = '-₹' + discount.toFixed(2);
                    document.getElementById('cart-final-total').textContent = '₹' + finalTotal.toFixed(2);
                    
                    const discountRow = document.getElementById('discount-row');
                    if (discount > 0) {
                        discountRow.classList.remove('hidden');
                    } else {
                        discountRow.classList.add('hidden');
                    }
                }
                
                // Initialize on page load
                document.addEventListener('DOMContentLoaded', function() {
                    updateCartTotal();
                });
            </script>
            @endif
        </div>
    @endif
</div>
@endsection
