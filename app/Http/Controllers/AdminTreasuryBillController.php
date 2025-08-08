<?php

namespace App\Http\Controllers;

use App\Models\TreasuryBill;
use Illuminate\Http\Request;

class AdminTreasuryBillController extends Controller
{
    public function index()
    {
        return response()->json(TreasuryBill::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'value' => 'required|string|unique:treasury_bills,value',
            'label' => 'required|string',
            'return' => 'required|numeric',
            'issue_number' => 'nullable|string',
            'auction_date' => 'nullable|date',
            'value_dated' => 'nullable|date',
        ]);
        $bill = TreasuryBill::create($validated);
        return response()->json($bill, 201);
    }

    public function update(Request $request, $id)
    {
        $bill = TreasuryBill::findOrFail($id);
        $validated = $request->validate([
            'value' => 'required|string|unique:treasury_bills,value,' . $id,
            'label' => 'required|string',
            'return' => 'required|numeric',
            'issue_number' => 'nullable|string',
            'auction_date' => 'nullable|date',
            'value_dated' => 'nullable|date',
        ]);
        $bill->update($validated);
        return response()->json($bill);
    }

    public function destroy($id)
    {
        $bill = TreasuryBill::findOrFail($id);
        $bill->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
