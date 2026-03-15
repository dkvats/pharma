<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaLibrary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaLibraryController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'image');
        $search = $request->get('search');

        $query = MediaLibrary::orderBy('created_at', 'desc');

        if ($type) {
            $query->where('file_type', $type);
        }

        if ($search) {
            $query->where('file_name', 'like', "%{$search}%");
        }

        $media = $query->paginate(24);

        return view('admin.media-library.index', compact('media', 'type', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $filePath = $file->store('media', 'public');
        $fileType = Str::startsWith($file->getMimeType(), 'image/') ? 'image' : 'document';
        $fileSize = $file->getSize();

        $media = MediaLibrary::create([
            'file_name'   => $fileName,
            'file_path'   => $filePath,
            'file_type'   => $fileType,
            'file_size'   => $fileSize,
            'alt_text'    => pathinfo($fileName, PATHINFO_FILENAME),
            'uploaded_by' => auth()->id(),
        ]);

        logActivity('Media Uploaded', 'MediaLibrary', $media->id, "Uploaded: {$fileName}");

        if ($request->ajax()) {
            return response()->json([
                'success'   => true,
                'id'        => $media->id,
                'url'       => $media->url,
                'file_name' => $media->file_name,
            ]);
        }

        return back()->with('success', "File '{$fileName}' uploaded successfully.");
    }

    public function update(Request $request, MediaLibrary $media)
    {
        $validated = $request->validate([
            'file_name' => 'required|string|max:255',
            'alt_text'  => 'nullable|string|max:255',
        ]);

        $media->update($validated);

        logActivity('Media Updated', 'MediaLibrary', $media->id, "Updated: {$media->file_name}");

        return back()->with('success', "Media updated successfully.");
    }

    public function destroy(MediaLibrary $media)
    {
        Storage::disk('public')->delete($media->file_path);
        $name = $media->file_name;
        $media->delete();

        logActivity('Media Deleted', 'MediaLibrary', $media->id, "Deleted: {$name}");
        return back()->with('success', "File '{$name}' deleted successfully.");
    }

    public function select(Request $request)
    {
        $type = $request->get('type', 'image');
        $media = MediaLibrary::where('file_type', $type)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'file_name', 'file_path', 'alt_text']);

        return response()->json($media->map(function ($m) {
            return [
                'id'        => $m->id,
                'file_name' => $m->file_name,
                'url'       => $m->url,
                'alt_text'  => $m->alt_text,
            ];
        }));
    }
}

