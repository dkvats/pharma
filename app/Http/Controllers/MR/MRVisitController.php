<?php

namespace App\Http\Controllers\MR;

use App\Http\Controllers\Controller;
use App\Models\MR\Doctor;
use App\Models\MR\DoctorVisit;
use App\Models\MRProductPromotion;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MRVisitController extends Controller
{
    public function index()
    {
        $visits = DoctorVisit::forMR(auth()->id())
            ->with('doctor')
            ->latest()
            ->paginate(15);

        return view('mr.visits.index', compact('visits'));
    }

    public function create()
    {
        $doctors  = Doctor::forMR(auth()->id())->active()->get();
        $products = Product::where('status', 'active')->orderBy('name')->get();
        return view('mr.visits.create', compact('doctors', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_id'          => 'required|exists:mr_doctors,id',
            'visit_date'         => 'required|date',
            'visit_time'         => 'nullable',
            'remarks'            => 'nullable|string',
            'products_discussed' => 'nullable|string',
            'next_visit_date'    => 'nullable|date|after_or_equal:visit_date',
            'photo'              => 'nullable|image|max:2048',
            'status'             => 'required|in:planned,completed,cancelled,rescheduled',
            'promoted_products'  => 'nullable|array',
            'promoted_products.*' => 'integer|exists:products,id',
            'promotion_notes'    => 'nullable|string',
        ]);

        $validated['mr_id'] = auth()->id();

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('mr/visits', 'public');
        }

        $visit = DoctorVisit::create($validated);

        // Save product promotions
        if (!empty($validated['promoted_products'])) {
            // Resolve doctor user_id from mr_doctor record
            $doctor    = Doctor::find($validated['doctor_id']);
            $doctorUserId = $doctor->user_id ?? null;

            foreach ($validated['promoted_products'] as $productId) {
                MRProductPromotion::create([
                    'mr_id'      => auth()->id(),
                    'doctor_id'  => $doctorUserId ?? $validated['doctor_id'],
                    'product_id' => $productId,
                    'visit_id'   => $visit->id,
                    'notes'      => $validated['promotion_notes'] ?? null,
                ]);
            }
        }

        return redirect()->route('mr.visits.index')
            ->with('success', 'Visit report added successfully.');
    }

    public function show(DoctorVisit $visit)
    {
        $this->authorizeAccess($visit);

        $visit->load('doctor');

        return view('mr.visits.show', compact('visit'));
    }

    public function edit(DoctorVisit $visit)
    {
        $this->authorizeAccess($visit);

        $doctors  = Doctor::forMR(auth()->id())->active()->get();
        $products = Product::where('status', 'active')->orderBy('name')->get();
        $visit->load('promotions.product');

        return view('mr.visits.edit', compact('visit', 'doctors', 'products'));
    }

    public function update(Request $request, DoctorVisit $visit)
    {
        $this->authorizeAccess($visit);

        $validated = $request->validate([
            'doctor_id'           => 'required|exists:mr_doctors,id',
            'visit_date'          => 'required|date',
            'visit_time'          => 'nullable',
            'remarks'             => 'nullable|string',
            'products_discussed'  => 'nullable|string',
            'next_visit_date'     => 'nullable|date|after_or_equal:visit_date',
            'photo'               => 'nullable|image|max:2048',
            'status'              => 'required|in:planned,completed,cancelled,rescheduled',
            'promoted_products'   => 'nullable|array',
            'promoted_products.*' => 'integer|exists:products,id',
            'promotion_notes'     => 'nullable|string',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($visit->photo_path) {
                Storage::disk('public')->delete($visit->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('mr/visits', 'public');
        }

        $visit->update($validated);

        // Sync product promotions: delete old ones for this visit, re-insert
        if ($request->has('promoted_products')) {
            MRProductPromotion::where('visit_id', $visit->id)->delete();

            $doctor      = Doctor::find($validated['doctor_id']);
            $doctorUserId = $doctor->user_id ?? $validated['doctor_id'];

            foreach ($validated['promoted_products'] as $productId) {
                MRProductPromotion::create([
                    'mr_id'      => auth()->id(),
                    'doctor_id'  => $doctorUserId,
                    'product_id' => $productId,
                    'visit_id'   => $visit->id,
                    'notes'      => $validated['promotion_notes'] ?? null,
                ]);
            }
        }

        return redirect()->route('mr.visits.index')
            ->with('success', 'Visit report updated successfully.');
    }

    public function destroy(DoctorVisit $visit)
    {
        $this->authorizeAccess($visit);

        // Delete photo if exists
        if ($visit->photo_path) {
            Storage::disk('public')->delete($visit->photo_path);
        }

        $visit->delete();

        return redirect()->route('mr.visits.index')
            ->with('success', 'Visit report deleted successfully.');
    }

    public function byDate($date)
    {
        $visits = DoctorVisit::forMR(auth()->id())
            ->whereDate('visit_date', $date)
            ->with('doctor')
            ->get();

        return view('mr.visits.by-date', compact('visits', 'date'));
    }

    private function authorizeAccess(DoctorVisit $visit)
    {
        if ($visit->mr_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }
    }
}
