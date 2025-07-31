<?php

namespace App\Http\Controllers;

use App\Models\Share;
use Illuminate\Http\Request;

class AdminShareController extends Controller
{
    public function index()
    {
        return response()->json(Share::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ticker' => 'required|string|unique:shares,ticker',
            'price' => 'required|numeric',
        ]);
        $share = Share::create($validated);
        return response()->json($share, 201);
    }

    public function update(Request $request, $id)
    {
        $share = Share::findOrFail($id);
        $validated = $request->validate([
            'ticker' => 'required|string|unique:shares,ticker,' . $id,
            'price' => 'required|numeric',
        ]);
        $share->update($validated);
        return response()->json($share);
    }

    public function destroy($id)
    {
        $share = Share::findOrFail($id);
        $share->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
