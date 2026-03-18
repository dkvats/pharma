<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.5; color: #333; }
        .container { padding: 30px; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { color: #2563eb; font-size: 28px; }
        .header p { color: #666; }
        .invoice-title { text-align: center; font-size: 24px; font-weight: bold; margin: 20px 0; }
        .info-grid { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .info-box { width: 48%; }
        .info-box h3 { background: #f3f4f6; padding: 8px; margin-bottom: 10px; font-size: 14px; }
        .info-box p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #f3f4f6; padding: 10px 8px; text-align: left; font-size: 11px; border-bottom: 2px solid #ddd; }
        td { padding: 8px; border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .totals { margin-top: 30px; width: 100%; }
        .totals-table { width: 50%; margin-left: auto; }
        .totals-table td { padding: 8px; border: none; }
        .totals-table .grand-total { font-size: 18px; font-weight: bold; border-top: 2px solid #333; }
        .gst-breakdown { margin: 20px 0; }
        .gst-breakdown h3 { margin-bottom: 10px; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666; font-size: 11px; }
        .amount-words { margin-top: 20px; font-style: italic; color: #666; }
        .signature { margin-top: 60px; text-align: right; }
        .signature-line { border-top: 1px solid #333; width: 200px; margin-left: auto; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $companyName }}</h1>
            <p>{{ $companyAddress }}</p>
            <p>GSTIN: {{ $companyGst }} | Phone: {{ $companyPhone }} | Email: {{ $companyEmail }}</p>
        </div>

        <div class="invoice-title">TAX INVOICE</div>

        <!-- Invoice & Customer Info -->
        <div class="info-grid">
            <div class="info-box">
                <h3>Invoice Details</h3>
                <p><strong>Invoice Number:</strong> {{ $invoice->invoice_number }}</p>
                <p><strong>Invoice Date:</strong> {{ $invoice->invoice_date->format('d M Y') }}</p>
                <p><strong>Order Number:</strong> {{ $invoice->order->order_number ?? 'N/A' }}</p>
                <p><strong>Status:</strong> {{ ucfirst($invoice->status) }}</p>
            </div>
            <div class="info-box">
                <h3>Customer Details</h3>
                <p><strong>Name:</strong> {{ $invoice->order->user->name ?? 'N/A' }}</p>
                <p><strong>Email:</strong> {{ $invoice->order->user->email ?? 'N/A' }}</p>
                <p><strong>Phone:</strong> {{ $invoice->order->user->phone ?? 'N/A' }}</p>
                <p><strong>Doctor/Referral:</strong> {{ $invoice->order->doctor->name ?? 'Direct Sale' }}</p>
            </div>
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Discount</th>
                    <th class="text-right">Taxable</th>
                    <th class="text-center">GST %</th>
                    <th class="text-right">GST Amt</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">₹{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">₹{{ number_format($item->discount_amount, 2) }}</td>
                    <td class="text-right">₹{{ number_format($item->taxable_value, 2) }}</td>
                    <td class="text-center">{{ $item->gst_percent }}%</td>
                    <td class="text-right">₹{{ number_format($item->gst_amount, 2) }}</td>
                    <td class="text-right">₹{{ number_format($item->total_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- GST Breakdown -->
        <div class="gst-breakdown">
            <h3>GST Breakdown</h3>
            <table>
                <thead>
                    <tr>
                        <th>GST Rate</th>
                        <th class="text-right">Taxable Value</th>
                        <th class="text-right">CGST</th>
                        <th class="text-right">SGST</th>
                        <th class="text-right">Total GST</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($gstBreakdown as $gst)
                    <tr>
                        <td>{{ $gst['rate_display'] }}</td>
                        <td class="text-right">₹{{ number_format($gst['taxable_value'], 2) }}</td>
                        <td class="text-right">₹{{ number_format($gst['cgst'], 2) }}</td>
                        <td class="text-right">₹{{ number_format($gst['sgst'], 2) }}</td>
                        <td class="text-right">₹{{ number_format($gst['total_gst'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="totals">
            <table class="totals-table">
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">₹{{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td>Discount:</td>
                    <td class="text-right">₹{{ number_format($invoice->discount_amount, 2) }}</td>
                </tr>
                <tr>
                    <td>Taxable Amount:</td>
                    <td class="text-right">₹{{ number_format($invoice->taxable_amount, 2) }}</td>
                </tr>
                <tr>
                    <td>GST Amount:</td>
                    <td class="text-right">₹{{ number_format($invoice->gst_amount, 2) }}</td>
                </tr>
                <tr class="grand-total">
                    <td>Grand Total:</td>
                    <td class="text-right">₹{{ number_format($invoice->grand_total, 2) }}</td>
                </tr>
            </table>
        </div>

        <div class="amount-words">
            <strong>Amount in words:</strong> {{ $invoice->getAmountInWords() }}
        </div>

        <!-- Signature -->
        <div class="signature">
            <div class="signature-line">Authorized Signature</div>
            <p>For {{ $companyName }}</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This is a computer generated invoice and does not require signature.</p>
            <p>Thank you for your business!</p>
        </div>
    </div>
</body>
</html>
