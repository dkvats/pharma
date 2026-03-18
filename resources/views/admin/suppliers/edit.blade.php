@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Supplier</h1>
        <form method="POST" action="{{ route('admin.suppliers.update', $supplier) }}" class="space-y-4">
            @csrf
            @method('PUT')
            @include('admin.suppliers.partials.form', ['supplier' => $supplier])
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.suppliers.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-lg">Cancel</a>
                <button class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
