@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
<div class="container mx-auto px-4 py-6 space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Suppliers</h1>
        <a href="{{ route('admin.suppliers.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">Add Supplier</a>
    </div>

    <form class="bg-white rounded-lg shadow p-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, phone, GST" class="w-full md:w-96 px-3 py-2 border rounded-lg">
    </form>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Phone</th>
                    <th class="px-4 py-3 text-left">GST</th>
                    <th class="px-4 py-3 text-left">Address</th>
                    <th class="px-4 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($suppliers as $supplier)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $supplier->name }}</td>
                    <td class="px-4 py-3">{{ $supplier->phone ?: '-' }}</td>
                    <td class="px-4 py-3">{{ $supplier->gst_no ?: '-' }}</td>
                    <td class="px-4 py-3">{{ $supplier->address ?: '-' }}</td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                        <form class="inline" method="POST" action="{{ route('admin.suppliers.destroy', $supplier) }}" onsubmit="return confirm('Delete this supplier?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:text-red-800">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No suppliers found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t">{{ $suppliers->links() }}</div>
    </div>
</div>
@endsection
