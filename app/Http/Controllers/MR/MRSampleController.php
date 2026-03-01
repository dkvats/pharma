<?php

namespace App\Http\Controllers\MR;

use App\Http\Controllers\Controller;
use App\Models\MR\Doctor;
use App\Models\MR\Sample;
use App\Models\Product;
use Illuminate\Http\Request;

class MRSampleController extends Controller
{
    public function index()
    {
        $samples = Sample::forMR(auth()->id())
            ->with(['doctor', 'product'])
            ->latest()
            ->paginate(15);

        return view('mr.samples.index', compact('samples'));
    }

    public function create()
    {
        $doctors = Doctor::forMR(auth()->id())->active()->get();
        $products = Product::where('status', 'active')->get();

        return view('mr.samples.create', compact('doctors', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:mr_doctors,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'given_date' => 'required|date',
            'remarks' => 'nullable|string',
            'batch_no' => 'nullable|string|max:50',
            'expiry_date' => 'nullable|date|after:given_date',
        ]);

        $validated['mr_id'] = auth()->id();

        Sample::create($validated);

        return redirect()->route('mr.samples.index')
            ->with('success', 'Sample record added successfully.');
    }

    public function show(Sample $sample)
    {
        $this->authorizeAccess($sample);

        $sample->load(['doctor', 'product']);

        return view('mr.samples.show', compact('sample'));
    }

    public function edit(Sample $sample)
    {
        $this->authorizeAccess($sample);

        $doctors = Doctor::forMR(auth()->id())->active()->get();
        $products = Product::where('status', 'active')->get();

        return view('mr.samples.edit', compact('sample', 'doctors', 'products'));
    }

    public function update(Request $request, Sample $sample)
    {
        $this->authorizeAccess($sample);

        $validated = $request->validate([
            'doctor_id' => 'required|exists:mr_doctors,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'given_date' => 'required|date',
            'remarks' => 'nullable|string',
            'batch_no' => 'nullable|string|max:50',
            'expiry_date' => 'nullable|date|after:given_date',
        ]);

        $sample->update($validated);

        return redirect()->route('mr.samples.index')
            ->with('success', 'Sample record updated successfully.');
    }

    public function destroy(Sample $sample)
    {
        $this->authorizeAccess($sample);

        $sample->delete();

        return redirect()->route('mr.samples.index')
            ->with('success', 'Sample record deleted successfully.');
    }

    public function byDoctor(Doctor $doctor)
    {
        $this->authorizeAccessToDoctor($doctor);

        $samples = Sample::forMR(auth()->id())
            ->where('doctor_id', $doctor->id)
            ->with('product')
            ->latest()
            ->paginate(15);

        return view('mr.samples.by-doctor', compact('samples', 'doctor'));
    }

    private function authorizeAccess(Sample $sample)
    {
        if ($sample->mr_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }
    }

    private function authorizeAccessToDoctor(Doctor $doctor)
    {
        if ($doctor->created_by !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }
    }
}
