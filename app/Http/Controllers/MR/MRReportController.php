<?php

namespace App\Http\Controllers\MR;

use App\Http\Controllers\Controller;
use App\Models\MR\Doctor;
use App\Models\MR\DoctorVisit;
use App\Models\MR\Order;
use App\Models\MR\Sample;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MRReportController extends Controller
{
    public function daily(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $mrId = auth()->id();

        $report = [
            'date' => $date,
            'doctors_visited' => DoctorVisit::forMR($mrId)->whereDate('visit_date', $date)->count(),
            'new_doctors' => Doctor::forMR($mrId)->whereDate('created_at', $date)->count(),
            'orders_placed' => Order::forMR($mrId)->whereDate('ordered_at', $date)->count(),
            'order_value' => Order::forMR($mrId)->whereDate('ordered_at', $date)->sum('total_amount'),
            'samples_given' => Sample::forMR($mrId)->whereDate('given_date', $date)->sum('quantity'),
            'visits' => DoctorVisit::forMR($mrId)->whereDate('visit_date', $date)->with('doctor')->get(),
            'orders' => Order::forMR($mrId)->whereDate('ordered_at', $date)->with('doctor')->get(),
        ];

        return view('mr.reports.daily', compact('report'));
    }

    public function weekly(Request $request)
    {
        $weekStart = $request->get('week_start') 
            ? Carbon::parse($request->get('week_start'))->startOfWeek() 
            : now()->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();
        $mrId = auth()->id();

        $report = [
            'week_start' => $weekStart->format('Y-m-d'),
            'week_end' => $weekEnd->format('Y-m-d'),
            'doctors_visited' => DoctorVisit::forMR($mrId)
                ->whereBetween('visit_date', [$weekStart, $weekEnd])->count(),
            'new_doctors' => Doctor::forMR($mrId)
                ->whereBetween('created_at', [$weekStart, $weekEnd])->count(),
            'orders_placed' => Order::forMR($mrId)
                ->whereBetween('ordered_at', [$weekStart, $weekEnd])->count(),
            'order_value' => Order::forMR($mrId)
                ->whereBetween('ordered_at', [$weekStart, $weekEnd])->sum('total_amount'),
            'samples_given' => Sample::forMR($mrId)
                ->whereBetween('given_date', [$weekStart, $weekEnd])->sum('quantity'),
            'daily_breakdown' => $this->getDailyBreakdown($mrId, $weekStart, $weekEnd),
        ];

        return view('mr.reports.weekly', compact('report'));
    }

    public function monthly(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $mrId = auth()->id();

        $report = [
            'month' => $month,
            'doctors_visited' => DoctorVisit::forMR($mrId)
                ->whereBetween('visit_date', [$startOfMonth, $endOfMonth])->count(),
            'unique_doctors_visited' => DoctorVisit::forMR($mrId)
                ->whereBetween('visit_date', [$startOfMonth, $endOfMonth])
                ->distinct('doctor_id')->count('doctor_id'),
            'new_doctors' => Doctor::forMR($mrId)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
            'orders_placed' => Order::forMR($mrId)
                ->whereBetween('ordered_at', [$startOfMonth, $endOfMonth])->count(),
            'order_value' => Order::forMR($mrId)
                ->whereBetween('ordered_at', [$startOfMonth, $endOfMonth])->sum('total_amount'),
            'samples_given' => Sample::forMR($mrId)
                ->whereBetween('given_date', [$startOfMonth, $endOfMonth])->sum('quantity'),
            'weekly_breakdown' => $this->getWeeklyBreakdown($mrId, $startOfMonth, $endOfMonth),
        ];

        return view('mr.reports.monthly', compact('report'));
    }

    public function doctors()
    {
        $mrId = auth()->id();

        $doctors = Doctor::forMR($mrId)
            ->withCount(['visits', 'orders', 'samples'])
            ->withSum('orders', 'total_amount')
            ->get()
            ->map(function ($doctor) {
                $doctor->last_visit = $doctor->visits()->latest()->first()?->visit_date;
                $doctor->total_order_value = $doctor->orders_sum_total_amount ?? 0;
                return $doctor;
            });

        return view('mr.reports.doctors', compact('doctors'));
    }

    public function performance()
    {
        $mrId = auth()->id();

        // Last 6 months performance
        $performance = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $performance[] = [
                'month' => $month->format('M Y'),
                'visits' => DoctorVisit::forMR($mrId)->thisMonth()->count(),
                'orders' => Order::forMR($mrId)->thisMonth()->count(),
                'order_value' => Order::forMR($mrId)->thisMonth()->sum('total_amount'),
                'new_doctors' => Doctor::forMR($mrId)->whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)->count(),
            ];
        }

        return view('mr.reports.performance', compact('performance'));
    }

    private function getDailyBreakdown($mrId, $start, $end)
    {
        $days = [];
        $current = $start->copy();

        while ($current <= $end) {
            $days[] = [
                'date' => $current->format('Y-m-d'),
                'day' => $current->format('D'),
                'visits' => DoctorVisit::forMR($mrId)->whereDate('visit_date', $current)->count(),
                'orders' => Order::forMR($mrId)->whereDate('ordered_at', $current)->count(),
                'order_value' => Order::forMR($mrId)->whereDate('ordered_at', $current)->sum('total_amount'),
            ];
            $current->addDay();
        }

        return $days;
    }

    private function getWeeklyBreakdown($mrId, $start, $end)
    {
        $weeks = [];
        $current = $start->copy()->startOfWeek();

        while ($current <= $end) {
            $weekEnd = $current->copy()->endOfWeek();
            $weeks[] = [
                'week' => 'Week ' . $current->weekOfMonth,
                'visits' => DoctorVisit::forMR($mrId)
                    ->whereBetween('visit_date', [$current, $weekEnd])->count(),
                'orders' => Order::forMR($mrId)
                    ->whereBetween('ordered_at', [$current, $weekEnd])->count(),
                'order_value' => Order::forMR($mrId)
                    ->whereBetween('ordered_at', [$current, $weekEnd])->sum('total_amount'),
            ];
            $current->addWeek();
        }

        return $weeks;
    }
}
