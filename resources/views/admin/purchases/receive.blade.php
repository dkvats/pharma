@extends('layouts.app')

@section('title', 'Receive Goods: ' . $purchase->order_number)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Receive Goods</h1>
                <p class="text-gray-600 font-mono">{{ $purchase->order_number }} - {{ $purchase->supplier->name }}</p>
            </div>
            <a href="{{ route('admin.purchases.show', $purchase) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-lg">Cancel</a>
        </div>

        <form method="POST" action="{{ route('admin.purchases.receive', $purchase) }}">
            @csrf

            <!-- Receipt Details -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Receipt Details</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Received Date *</label>
                        <input type="date" name="received_date" value="{{ date('Y-m-d') }}" required
                            class="w-full px-3 py-2 border rounded-lg @error('received_date') border-red-500 @enderror">
                        @error('received_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" class="w-full px-3 py-2 border rounded-lg">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Items to Receive -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Items to Receive</h3>
                
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Ordered</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Already Received</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Remaining</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Receive Now</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($purchase->items as $item)
                        @if(!$item->isFullyReceived())
                        <tr>
                            <td class="px-4 py-3">
                                {{ $item->product->name }}
                                <input type="hidden" name="items[{{ $loop->index }}][purchase_order_item_id]" value="{{ $item->id }}">
                            </td>
                            <td class="px-4 py-3 text-center">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-center">{{ $item->received_quantity }}</td>
                            <td class="px-4 py-3 text-center font-semibold text-blue-600">{{ $item->remaining_quantity }}</td>
                            <td class="px-4 py-3 text-center">
                                <input type="number" name="items[{{ $loop->index }}][received_quantity]" 
                                    value="0" min="0" max="{{ $item->remaining_quantity }}"
                                    class="w-20 px-2 py-1 text-center border rounded-lg focus:ring-2 focus:ring-blue-500">
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>

                @if($purchase->items->every(fn($item) => $item->isFullyReceived()))
                    <div class="text-center py-8 text-gray-500">
                        All items have been fully received.
                    </div>
                @endif

                @error('items')<p class="text-red-500 text-xs mt-4">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.purchases.show', $purchase) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-6 rounded-lg">Cancel</a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white py-2 px-6 rounded-lg">Confirm Receipt & Create Batches</button>
            </div>
        </form>
    </div>
</div>
@endsection
