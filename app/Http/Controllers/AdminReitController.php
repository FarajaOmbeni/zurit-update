<?php

namespace App\Http\Controllers;

use App\Models\Reit;
use Illuminate\Http\Request;

class AdminReitController extends Controller
{
    public function index()
    {
        return response()->json(Reit::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
        ]);
        $reit = Reit::create($validated);
        return response()->json($reit, 201);
    }

    public function update(Request $request, $id)
    {
        $reit = Reit::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
        ]);
        $reit->update($validated);
        return response()->json($reit);
    }

    public function destroy($id)
    {
        $reit = Reit::findOrFail($id);
        $reit->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
