<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bill - {{ $order->order_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; line-height: 1.5; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .company-name { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
        .bill-title { font-size: 18px; margin-top: 10px; }
        .info-section { margin-bottom: 20px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .info-label { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; font-weight: bold; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">Pharma Management System</div>
        <div>Invoice/Bill</div>
        <div class="bill-title">Bill #{{ $order->order_number }}</div>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span><span class="info-label">Order Number:</span> {{ $order->order_number }}</span>
            <span><span class="info-label">Date:</span> {{ $order->created_at->format('M d, Y') }}</span>
        </div>
        <div class="info-row">
            <span><span class="info-label">Status:</span> {{ $order->status_label }}</span>
            <span><span class="info-label">Sale Type:</span> {{ $order->sale_type_label }}</span>
        </div>
    </div>

    <div class="info-section">
        <div class="info-label">Customer Information:</div>
        <div>Name: {{ $order->user->name ?? 'N/A' }}</div>
        <div>Email: {{ $order->user->email ?? 'N/A' }}</div>
        <div>Phone: {{ $order->user->phone ?? 'N/A' }}</div>
    </div>

    @if($order->doctor)
    <div class="info-section">
        <div class="info-label">Doctor:</div>
        <div>{{ $order->doctor->name }} ({{ $order->doctor->code ?? 'N/A' }})</div>
    </div>
    @endif

    @if($order->store)
    <div class="info-section">
        <div class="info-label">Store:</div>
        <div>{{ $order->store->name }} ({{ $order->store->code ?? 'N/A' }})</div>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th class="text-right">Price</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product->name ?? 'N/A' }}</td>
                <td class="text-right">&#8377;{{ number_format($item->price, 2) }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">&#8377;{{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-right">Total Amount:</td>
                <td class="text-right">&#8377;{{ number_format($order->total_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    @if($order->notes)
    <div class="info-section" style="margin-top: 20px; padding: 12px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9;">
        <div style="font-weight: bold; margin-bottom: 5px;">Doctor Remarks:</div>
        <div style="color: #666; margin: 0;">{{ $order->notes }}</div>
    </div>
    @endif

    <div class="footer">
        <p>Generated on {{ now()->format('F d, Y h:i A') }}</p>
        <p>Thank you for your business!</p>
    </div>
</body>
</html>
