@extends('layouts.app')

@section('title', 'Expiry Return Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Expiry Return Management</h1>
            <p class="text-sm text-gray-500 mt-1">Track expired batches pending return or disposal</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.expired-batches.download', ['status' => $status]) }}"
               class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 px-4 rounded-lg">
                Download CSV
            </a>
            <a href="{{ route('admin.inventory-reports.index') }}" class="text-blue-600 hover:text-blue-800 text-sm self-center">
                &larr; Inventory Reports
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Status Tabs -->
    <div class="flex gap-2 mb-6 flex-wrap">
        @foreach(['pending_return' => ['label' => 'Pending Return', 'color' => 'yellow'], 'returned' => ['label' => 'Returned', 'color' => 'green'], 'disposed' => ['label' => 'Disposed', 'color' => 'gray'], 'all' => ['label' => 'All', 'color' => 'blue']] as $key => $cfg)
        <a href="{{ route('admin.expired-batches.index', ['status' => $key]) }}"
           class="px-4 py-2 rounded-lg text-sm font-semibold border
               {{ $status === $key
                   ? 'bg-'.$cfg['color'].'-600 text-white border-'.$cfg['color'].'-600'
                   : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
            {{ $cfg['label'] }}
            <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full bg-white bg-opacity-30">{{ $counts[$key] }}</span>
        </a>
        @endforeach
    </div>

    <!-- Bulk Actions Form -->
    <form id="bulkForm" action="{{ route('admin.expired-batches.bulk-update') }}" method="POST">
        @csrf

        <div class="flex gap-2 mb-3 items-center">
            <button type="button" onclick="selectAll()" class="text-sm text-blue-600 hover:underline">Select All</button>
            <span class="text-gray-400">|</span>
            <button type="button" onclick="clearAll()" class="text-sm text-gray-500 hover:underline">Clear</button>
            <span class="flex-1"></span>
            <button type="submit" name="action" value="returned"
                class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold py-1.5 px-3 rounded-lg">
                Mark Selected Returned
            </button>
            <button type="submit" name="action" value="disposed"
                class="bg-gray-600 hover:bg-gray-700 text-white text-xs font-semibold py-1.5 px-3 rounded-lg">
                Mark Selected Disposed
            </button>
        </div>

        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left w-10"><input type="checkbox" id="selectAllBox" onchange="toggleAll(this)"></th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiry Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recorded</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($expiredBatches as $eb)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <input type="checkbox" name="ids[]" value="{{ $eb->id }}" class="row-checkbox">
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-900">{{ $eb->product->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $eb->product->category ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3 font-mono text-sm text-gray-800">{{ $eb->batch_number }}</td>
                        <td class="px-4 py-3 text-sm text-red-700 font-medium">{{ $eb->expiry_date->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $eb->quantity }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $eb->status_badge_class }}">
                                {{ $eb->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $eb->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            @if($eb->status === 'pending_return')
                            <form action="{{ route('admin.expired-batches.mark-returned', $eb) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-800 text-xs font-semibold mr-2">Return</button>
                            </form>
                            <form action="{{ route('admin.expired-batches.mark-disposed', $eb) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-600 hover:text-gray-800 text-xs font-semibold">Dispose</button>
                            </form>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            No expired batches found for this status.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <div class="mt-4">
        {{ $expiredBatches->links() }}
    </div>
</div>

<script>
function toggleAll(cb) {
    document.querySelectorAll('.row-checkbox').forEach(c => c.checked = cb.checked);
}
function selectAll() {
    document.getElementById('selectAllBox').checked = true;
    document.querySelectorAll('.row-checkbox').forEach(c => c.checked = true);
}
function clearAll() {
    document.getElementById('selectAllBox').checked = false;
    document.querySelectorAll('.row-checkbox').forEach(c => c.checked = false);
}
</script>
@endsection
