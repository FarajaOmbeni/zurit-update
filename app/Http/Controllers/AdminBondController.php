<?php

namespace App\Http\Controllers;

use App\Models\Bond;
use Illuminate\Http\Request;

class AdminBondController extends Controller
{
    public function index()
    {
        return response()->json(Bond::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'value' => 'required|string|unique:bonds,value',
            'label' => 'required|string',
            'return' => 'required|numeric',
        ]);
        $bond = Bond::create($validated);
        return response()->json($bond, 201);
    }

    public function update(Request $request, $id)
    {
        $bond = Bond::findOrFail($id);
        $validated = $request->validate([
            'value' => 'required|string|unique:bonds,value,' . $id,
            'label' => 'required|string',
            'return' => 'required|numeric',
        ]);
        $bond->update($validated);
        return response()->json($bond);
    }

    public function destroy($id)
    {
        $bond = Bond::findOrFail($id);
        $bond->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
