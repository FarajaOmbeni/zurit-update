<?php

namespace App\Http\Controllers;

use App\Models\RealEstate;
use Illuminate\Http\Request;

class AdminRealEstateController extends Controller
{
    public function index()
    {
        return response()->json(RealEstate::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'value' => 'required|string|unique:real_estates,value',
            'label' => 'required|string',
        ]);
        $estate = RealEstate::create($validated);
        return response()->json($estate, 201);
    }

    public function update(Request $request, $id)
    {
        $estate = RealEstate::findOrFail($id);
        $validated = $request->validate([
            'value' => 'required|string|unique:real_estates,value,' . $id,
            'label' => 'required|string',
        ]);
        $estate->update($validated);
        return response()->json($estate);
    }

    public function destroy($id)
    {
        $estate = RealEstate::findOrFail($id);
        $estate->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
