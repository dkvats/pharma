@extends('layouts.app')

@section('title', 'Spin Settings')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Spin System Settings</h1>
        <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded">
            Back to Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Spin Eligibility Rules</h2>
        
        <div class="bg-blue-50 rounded-lg p-4 mb-6">
            <p class="text-sm text-blue-800">
                <strong>Current Rule:</strong> Doctors must complete 30 delivered sales AND sell at least 1 unit of the specific product below to be eligible for a spin.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.settings.spin.update') }}">
            @csrf
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Required Product for Spin</label>
                <select name="spin_target_product_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- No Specific Product Required --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ $currentProductId == $product->id ? 'selected' : '' }}>
                            {{ $product->name }} ({{ $product->sku ?? 'No SKU' }})
                        </option>
                    @endforeach
                </select>
                <p class="text-sm text-gray-500 mt-1">
                    Select a product that doctors must sell to qualify for spins. Leave empty to only require 30 sales.
                </p>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
