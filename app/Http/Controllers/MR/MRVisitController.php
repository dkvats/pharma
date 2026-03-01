<?php

namespace App\Http\Controllers\MR;

use App\Http\Controllers\Controller;
use App\Models\MR\Doctor;
use App\Models\MR\DoctorVisit;
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
        $doctors = Doctor::forMR(auth()->id())->active()->get();
        return view('mr.visits.create', compact('doctors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:mr_doctors,id',
            'visit_date' => 'required|date',
            'visit_time' => 'nullable',
            'remarks' => 'nullable|string',
            'products_discussed' => 'nullable|string',
            'next_visit_date' => 'nullable|date|after_or_equal:visit_date',
            'photo' => 'nullable|image|max:2048',
            'status' => 'required|in:planned,completed,cancelled,rescheduled',
        ]);

        $validated['mr_id'] = auth()->id();

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('mr/visits', 'public');
        }

        DoctorVisit::create($validated);

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

        $doctors = Doctor::forMR(auth()->id())->active()->get();

        return view('mr.visits.edit', compact('visit', 'doctors'));
    }

    public function update(Request $request, DoctorVisit $visit)
    {
        $this->authorizeAccess($visit);

        $validated = $request->validate([
            'doctor_id' => 'required|exists:mr_doctors,id',
            'visit_date' => 'required|date',
            'visit_time' => 'nullable',
            'remarks' => 'nullable|string',
            'products_discussed' => 'nullable|string',
            'next_visit_date' => 'nullable|date|after_or_equal:visit_date',
            'photo' => 'nullable|image|max:2048',
            'status' => 'required|in:planned,completed,cancelled,rescheduled',
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
