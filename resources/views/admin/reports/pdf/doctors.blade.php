<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Doctor Performance Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { color: #333; border-bottom: 2px solid #333; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; font-weight: bold; }
        .date-range { color: #666; font-style: italic; margin-bottom: 20px; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h1>Doctor Performance Report</h1>
    
    <div class="date-range">
        Period: {{ $startDate ? $startDate : 'All Time' }} to {{ $endDate ? $endDate : 'Present' }}
    </div>
    
    <h2>Doctor Sales Summary</h2>
    <table>
        <thead>
            <tr>
                <th>Doctor Name</th>
                <th>Code</th>
                <th class="text-right">Total Orders</th>
                <th class="text-right">Total Sales</th>
                <th class="text-right">Commission</th>
            </tr>
        </thead>
        <tbody>
            @forelse($doctorPerformance as $doctor)
                <tr>
                    <td>{{ $doctor->name }}</td>
                    <td>{{ $doctor->code }}</td>
                    <td class="text-right">{{ $doctor->total_orders }}</td>
                    <td class="text-right">₹{{ number_format($doctor->total_sales, 2) }}</td>
                    <td class="text-right">₹{{ number_format($doctor->total_commission, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">No data available</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right; color: #666; font-size: 10px;">
        Generated on {{ now()->format('F d, Y h:i A') }}
    </div>
</body>
</html>
