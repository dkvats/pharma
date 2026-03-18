@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
<div class="container mx-auto px-4 py-6 space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">GST Invoices</h1>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form class="flex flex-wrap gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search invoice/order number" class="px-3 py-2 border rounded-lg">
            <select name="status" class="px-3 py-2 border rounded-lg">
                <option value="">All Status</option>
                <option value="generated" {{ request('status') == 'generated' ? 'selected' : '' }}>Generated</option>
                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-3 py-2 border rounded-lg" placeholder="From">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-3 py-2 border rounded-lg" placeholder="To">
            <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg">Filter</button>
        </form>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Invoice #</th>
                    <th class="px-4 py-3 text-left">Order #</th>
                    <th class="px-4 py-3 text-left">Customer</th>
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-right">Amount</th>
                    <th class="px-4 py-3 text-right">GST</th>
                    <th class="px-4 py-3 text-right">Total</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($invoices as $invoice)
                <tr>
                    <td class="px-4 py-3 font-mono font-semibold">{{ $invoice->invoice_number }}</td>
                    <td class="px-4 py-3 font-mono text-sm">{{ $invoice->order->order_number ?? 'N/A' }}</td>
                    <td class="px-4 py-3">{{ $invoice->order->user->name ?? 'N/A' }}</td>
                    <td class="px-4 py-3">{{ $invoice->invoice_date->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-right">₹{{ number_format($invoice->taxable_amount, 2) }}</td>
                    <td class="px-4 py-3 text-right">₹{{ number_format($invoice->gst_amount, 2) }}</td>
                    <td class="px-4 py-3 text-right font-semibold">₹{{ number_format($invoice->grand_total, 2) }}</td>
                    <td class="px-4 py-3 text-center">
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
                    </td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-blue-600 hover:text-blue-800">View</a>
                        <a href="{{ route('admin.invoices.view', $invoice) }}" target="_blank" class="text-green-600 hover:text-green-800">PDF</a>
                        <a href="{{ route('admin.invoices.download', $invoice) }}" class="text-purple-600 hover:text-purple-800">Download</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-4 py-8 text-center text-gray-500">No invoices found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t">{{ $invoices->links() }}</div>
    </div>
</div>
@endsection
