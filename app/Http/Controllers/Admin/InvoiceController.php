<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Display list of invoices
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['order.user', 'order.doctor', 'items.product']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($oq) use ($search) {
                      $oq->where('order_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        $invoices = $query->latest()->paginate(15)->withQueryString();

        return view('admin.invoices.index', compact('invoices'));
    }

    /**
     * Display invoice details
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['order.user', 'order.doctor', 'order.store', 'items.product', 'creator']);
        $gstBreakdown = $this->invoiceService->getGstBreakdownForDisplay($invoice);

        return view('admin.invoices.show', compact('invoice', 'gstBreakdown'));
    }

    /**
     * Download invoice as PDF
     */
    public function download(Invoice $invoice)
    {
        $invoice->load(['order.user', 'order.doctor', 'order.store', 'items.product', 'creator']);
        $gstBreakdown = $this->invoiceService->getGstBreakdownForDisplay($invoice);

        // Get company settings
        $companyName = config('app.name', 'Pharma Company');
        $companyAddress = '123, Pharma Street, Medical City - 400001';
        $companyGst = '27AABCU9603R1ZX';
        $companyPhone = '+91 12345 67890';
        $companyEmail = 'info@pharma.com';

        $pdf = PDF::loadView('admin.invoices.pdf', compact(
            'invoice',
            'gstBreakdown',
            'companyName',
            'companyAddress',
            'companyGst',
            'companyPhone',
            'companyEmail'
        ));

        $filename = 'Invoice_' . $invoice->invoice_number . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * View invoice PDF in browser
     */
    public function view(Invoice $invoice)
    {
        $invoice->load(['order.user', 'order.doctor', 'order.store', 'items.product', 'creator']);
        $gstBreakdown = $this->invoiceService->getGstBreakdownForDisplay($invoice);

        // Get company settings
        $companyName = config('app.name', 'Pharma Company');
        $companyAddress = '123, Pharma Street, Medical City - 400001';
        $companyGst = '27AABCU9603R1ZX';
        $companyPhone = '+91 12345 67890';
        $companyEmail = 'info@pharma.com';

        $pdf = PDF::loadView('admin.invoices.pdf', compact(
            'invoice',
            'gstBreakdown',
            'companyName',
            'companyAddress',
            'companyGst',
            'companyPhone',
            'companyEmail'
        ));

        return $pdf->stream('Invoice_' . $invoice->invoice_number . '.pdf');
    }

    /**
     * Regenerate invoice (for corrections)
     */
    public function regenerate(Invoice $invoice)
    {
        $this->invoiceService->regenerateInvoice($invoice);

        return back()->with('success', 'Invoice regenerated successfully.');
    }

    /**
     * Cancel invoice
     */
    public function cancel(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'Cannot cancel a paid invoice.');
        }

        $invoice->status = 'cancelled';
        $invoice->save();

        return back()->with('success', 'Invoice cancelled successfully.');
    }
}
