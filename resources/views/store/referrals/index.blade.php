@extends('layouts.app')

@section('title', 'Referral Doctors')
@section('page-title', 'Doctor Referrals')
@section('page-description', 'Doctors who have referred sales to your store')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Doctor Referrals</h2>
            <p class="text-gray-600 mt-1">Track sales from doctor referrals</p>
        </div>
        <a href="{{ route('store.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Dashboard
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-card p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Referral Doctors</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $referralDoctors->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-card p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="shopping-bag" class="w-6 h-6 text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Referral Sales</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($referralDoctors->sum('total_sales'), 0) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-card p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="indian-rupee" class="w-6 h-6 text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">₹{{ number_format($referralDoctors->sum('total_amount'), 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Doctors Table -->
    <div class="bg-white rounded-xl shadow-card border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referral Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sales</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($referralDoctors as $doctor)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                        <i data-lucide="user-md" class="w-5 h-5 text-purple-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $doctor->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $doctor->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <code class="px-2 py-1 bg-gray-100 rounded text-sm font-mono">{{ $doctor->unique_code }}</code>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900">{{ $doctor->total_sales }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-gray-900">₹{{ number_format($doctor->total_amount, 2) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-100">
                                    <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                                    View
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i data-lucide="users" class="w-12 h-12 mx-auto mb-3 opacity-50"></i>
                                    <p class="text-lg font-medium text-gray-900">No referral doctors yet</p>
                                    <p class="text-sm text-gray-500 mt-1">Doctors who refer sales will appear here</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($referralDoctors->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $referralDoctors->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
