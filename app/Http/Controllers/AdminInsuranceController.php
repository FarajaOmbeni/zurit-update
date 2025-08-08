<?php

namespace App\Http\Controllers;

use App\Models\Insurance;
use Illuminate\Http\Request;

class AdminInsuranceController extends Controller
{
    public function index()
    {
        return response()->json(Insurance::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'value' => 'required|string|unique:insurances,value',
            'policies' => 'required|array',
        ]);
        $insurance = Insurance::create([
            'name' => $validated['name'],
            'value' => $validated['value'],
            'policies' => json_encode($validated['policies']),
        ]);
        return response()->json($insurance, 201);
    }

    public function update(Request $request, $id)
    {
        $insurance = Insurance::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string',
            'value' => 'required|string|unique:insurances,value,' . $id,
            'policies' => 'required|array',
        ]);
        $insurance->update([
            'name' => $validated['name'],
            'value' => $validated['value'],
            'policies' => json_encode($validated['policies']),
        ]);
        return response()->json($insurance);
    }

    public function destroy($id)
    {
        $insurance = Insurance::findOrFail($id);
        $insurance->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
