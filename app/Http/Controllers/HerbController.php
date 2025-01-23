<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Herb;

class HerbController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Herb::all());
    }

    public function show($id)
    {
        $herb = Herb::find($id);

        if (!$herb) {
            return response()->json(['message' => 'Herb not found'], 404);
        }

        return response()->json($herb);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'usage' => 'required|string',
            'image_url' => 'nullable|string',
            'price' => 'nullable|numeric',
        ]);

        $herb = Herb::create($validated);

        return response()->json($herb, 201);
    }

    public function update(Request $request, $id)
    {
        $herb = Herb::find($id);

        if (!$herb) {
            return response()->json(['message' => 'Herb not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'usage' => 'nullable|string',
            'image_url' => 'nullable|string',
            'price' => 'nullable|numeric',
        ]);

        $herb->update($validated);

        return response()->json($herb);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $herb = Herb::find($id);

        if (!$herb) {
            return response()->json(['message' => 'Herb not found'], 404);
        }

        $herb->delete();

        return response()->json(['message' => 'Herb deleted successfully']);
    }
}
