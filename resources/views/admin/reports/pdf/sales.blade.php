<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { color: #333; border-bottom: 2px solid #333; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; font-weight: bold; }
        .summary-box { background: #f9f9f9; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .summary-item { display: inline-block; margin-right: 30px; }
        .summary-label { font-weight: bold; color: #666; }
        .summary-value { font-size: 18px; color: #333; }
        .date-range { color: #666; font-style: italic; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Sales Report</h1>
    
    <div class="date-range">
        Period: {{ $startDate ? $startDate : 'All Time' }} to {{ $endDate ? $endDate : 'Present' }}
    </div>
    
    <div class="summary-box">
        <div class="summary-item">
            <div class="summary-label">Total Revenue</div>
            <div class="summary-value">₹{{ number_format($summary['total_revenue'], 2) }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Orders</div>
            <div class="summary-value">{{ $summary['total_orders'] }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Commission</div>
            <div class="summary-value">₹{{ number_format($summary['total_commission'], 2) }}</div>
        </div>
    </div>

    <h2>Sales by Type</h2>
    <table>
        <thead>
            <tr>
                <th>Sale Type</th>
                <th>Orders</th>
                <th>Revenue</th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesByType as $type => $data)
                <tr>
                    <td>{{ ucwords(str_replace('_', ' ', $type)) }}</td>
                    <td>{{ $data['count'] }}</td>
                    <td>₹{{ number_format($data['total'], 2) }}</td>
                    <td>{{ $summary['total_revenue'] > 0 ? round(($data['total'] / $summary['total_revenue']) * 100, 1) : 0 }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Top Products</h2>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Quantity Sold</th>
                <th>Revenue</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topProducts as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category }}</td>
                    <td>{{ $product->total_quantity }}</td>
                    <td>₹{{ number_format($product->total_revenue, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right; color: #666; font-size: 10px;">
        Generated on {{ now()->format('F d, Y h:i A') }}
    </div>
</body>
</html>
