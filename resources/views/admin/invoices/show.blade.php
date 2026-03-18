@extends('layouts.app')

@section('title', 'Invoice: ' . $invoice->invoice_number)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">GST Invoice</h1>
                <p class="text-gray-600 font-mono">{{ $invoice->invoice_number }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.invoices.view', $invoice) }}" target="_blank" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg">View PDF</a>
                <a href="{{ route('admin.invoices.download', $invoice) }}" class="bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg">Download PDF</a>
                <a href="{{ route('admin.invoices.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-lg">Back</a>
            </div>
        </div>

        <!-- Invoice Info -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="text-xs text-gray-500 uppercase">Invoice Date</label>
                    <p class="font-semibold">{{ $invoice->invoice_date->format('d M Y') }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase">Order Number</label>
                    <p class="font-semibold font-mono">{{ $invoice->order->order_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase">Status</label>
                    <p>
                        @php
                            $statusColors = [
                                'generated' => 'bg-blue-100 text-blue-800',
                                'sent' => 'bg-yellow-100 text-yellow-800',
                                'paid' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $statusColors[$invoice->status] ?? 'bg-gray-100' }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase">Created By</label>
                    <p class="font-semibold">{{ $invoice->creator->name ?? 'System' }}</p>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Customer Details</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs text-gray-500 uppercase">Name</label>
                    <p class="font-semibold">{{ $invoice->order->user->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase">Email</label>
                    <p class="font-semibold">{{ $invoice->order->user->email ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase">Phone</label>
                    <p class="font-semibold">{{ $invoice->order->user->phone ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase">Doctor/Referral</label>
                    <p class="font-semibold">{{ $invoice->order->doctor->name ?? 'Direct Sale' }}</p>
                </div>
            </div>
        </div>

        <!-- Items -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Invoice Items</h3>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Discount</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Taxable</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">GST %</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">GST Amt</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($invoice->items as $item)
                    <tr>
                        <td class="px-4 py-3">{{ $item->product_name }}</td>
                        <td class="px-4 py-3 text-center">{{ $item->quantity }}</td>
                        <td class="px-4 py-3 text-right">₹{{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-4 py-3 text-right">₹{{ number_format($item->discount_amount, 2) }}</td>
                        <td class="px-4 py-3 text-right">₹{{ number_format($item->taxable_value, 2) }}</td>
                        <td class="px-4 py-3 text-center">{{ $item->gst_percent }}%</td>
                        <td class="px-4 py-3 text-right">₹{{ number_format($item->gst_amount, 2) }}</td>
                        <td class="px-4 py-3 text-right font-semibold">₹{{ number_format($item->total_amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- GST Breakdown -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">GST Breakdown</h3>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">GST Rate</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Taxable Value</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">CGST</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">SGST</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total GST</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($gstBreakdown as $gst)
                    <tr>
                        <td class="px-4 py-3">{{ $gst['rate_display'] }}</td>
                        <td class="px-4 py-3 text-right">₹{{ number_format($gst['taxable_value'], 2) }}</td>
                        <td class="px-4 py-3 text-right">₹{{ number_format($gst['cgst'], 2) }}</td>
                        <td class="px-4 py-3 text-right">₹{{ number_format($gst['sgst'], 2) }}</td>
                        <td class="px-4 py-3 text-right font-semibold">₹{{ number_format($gst['total_gst'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-end">
                <div class="w-full md:w-1/2">
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-semibold">₹{{ number_format($invoice->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Discount</span>
                        <span class="font-semibold">₹{{ number_format($invoice->discount_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Taxable Amount</span>
                        <span class="font-semibold">₹{{ number_format($invoice->taxable_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">GST Amount</span>
                        <span class="font-semibold">₹{{ number_format($invoice->gst_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-3 text-xl font-bold">
                        <span>Grand Total</span>
                        <span>₹{{ number_format($invoice->grand_total, 2) }}</span>
                    </div>
                    <div class="text-sm text-gray-500 mt-2">
                        Amount in words: {{ $invoice->getAmountInWords() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
