<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Show main reports dashboard
     */
    public function dashboard()
    {
        $stats = $this->reportService->getDashboardStats();
        $monthlyTrend = $this->reportService->getMonthlyTrend(6);
        $salesByType = $this->reportService->getSalesByType();
        $lowStockProducts = $this->reportService->getLowStockProducts();

        return view('admin.reports.dashboard', compact(
            'stats',
            'monthlyTrend',
            'salesByType',
            'lowStockProducts'
        ));
    }

    /**
     * Show sales report
     */
    public function sales(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $summary = $this->reportService->getSalesSummary($startDate, $endDate);
        $salesByType = $this->reportService->getSalesByType($startDate, $endDate);
        $topProducts = $this->reportService->getTopProducts(10, $startDate, $endDate);

        return view('admin.reports.sales', [
            'summary' => $summary,
            'salesByType' => $salesByType,
            'topProducts' => $topProducts,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Show doctor performance report
     */
    public function doctors(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $doctorPerformance = $this->reportService->getDoctorPerformance(null, $startDate, $endDate);

        // Force safe data passing - guarantee Blade never receives null
        return view('admin.reports.doctors', [
            'reports' => $doctorPerformance ?? collect(),
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Show store performance report
     */
    public function stores(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $storePerformance = $this->reportService->getStorePerformance(null, $startDate, $endDate);

        // Force safe data passing - guarantee Blade never receives null
        return view('admin.reports.stores', [
            'reports' => $storePerformance ?? collect(),
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Export sales report as PDF
     */
    public function exportSalesPdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $summary = $this->reportService->getSalesSummary($startDate, $endDate);
        $salesByType = $this->reportService->getSalesByType($startDate, $endDate);
        $topProducts = $this->reportService->getTopProducts(10, $startDate, $endDate);

        $pdf = PDF::loadView('admin.reports.pdf.sales', compact(
            'summary',
            'salesByType',
            'topProducts',
            'startDate',
            'endDate'
        ));

        return $pdf->download('sales-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export sales report as Excel
     */
    public function exportSalesExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $topProducts = $this->reportService->getTopProducts(10, $startDate, $endDate);

        $data = [];
        foreach ($topProducts as $product) {
            $data[] = [
                'Product Name' => $product->name,
                'Category' => $product->category,
                'Total Quantity' => $product->total_quantity,
                'Total Revenue' => '$' . number_format($product->total_revenue, 2),
            ];
        }

        return Excel::download(new class($data) implements FromArray, WithHeadings, ShouldAutoSize {
            protected $data;
            public function __construct($data) { $this->data = $data; }
            public function array(): array { return $this->data; }
            public function headings(): array { return ['Product Name', 'Category', 'Total Quantity', 'Total Revenue']; }
        }, 'sales-report-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Export doctors report as PDF
     */
    public function exportDoctorsPdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $doctorPerformance = $this->reportService->getDoctorPerformance(null, $startDate, $endDate);

        $pdf = PDF::loadView('admin.reports.pdf.doctors', compact(
            'doctorPerformance',
            'startDate',
            'endDate'
        ));

        return $pdf->download('doctors-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export doctors report as Excel
     */
    public function exportDoctorsExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $doctorPerformance = $this->reportService->getDoctorPerformance(null, $startDate, $endDate);

        $data = [];
        foreach ($doctorPerformance as $doctor) {
            $data[] = [
                'Doctor Name' => $doctor->name,
                'Code' => $doctor->code,
                'Total Orders' => $doctor->total_orders,
                'Total Sales' => '$' . number_format($doctor->total_sales, 2),
                'Commission' => '$' . number_format($doctor->total_commission, 2),
            ];
        }

        return Excel::download(new class($data) implements FromArray, WithHeadings, ShouldAutoSize {
            protected $data;
            public function __construct($data) { $this->data = $data; }
            public function array(): array { return $this->data; }
            public function headings(): array { return ['Doctor Name', 'Code', 'Total Orders', 'Total Sales', 'Commission']; }
        }, 'doctors-report-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Export stores report as PDF
     */
    public function exportStoresPdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $storePerformance = $this->reportService->getStorePerformance(null, $startDate, $endDate);

        $pdf = PDF::loadView('admin.reports.pdf.stores', compact(
            'storePerformance',
            'startDate',
            'endDate'
        ));

        return $pdf->download('stores-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export stores report as Excel
     */
    public function exportStoresExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $storePerformance = $this->reportService->getStorePerformance(null, $startDate, $endDate);

        $data = [];
        foreach ($storePerformance as $store) {
            $data[] = [
                'Store Name' => $store->name,
                'Code' => $store->code,
                'Total Orders' => $store->total_orders,
                'Total Sales' => '$' . number_format($store->total_sales, 2),
                'Commission' => '$' . number_format($store->total_commission, 2),
            ];
        }

        return Excel::download(new class($data) implements FromArray, WithHeadings, ShouldAutoSize {
            protected $data;
            public function __construct($data) { $this->data = $data; }
            public function array(): array { return $this->data; }
            public function headings(): array { return ['Store Name', 'Code', 'Total Orders', 'Total Sales', 'Commission']; }
        }, 'stores-report-' . now()->format('Y-m-d') . '.xlsx');
    }
}
