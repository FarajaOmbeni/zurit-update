<?php

namespace App\Http\Controllers;

use App\Models\MoneyMarketFund;
use Illuminate\Http\Request;

class AdminMoneyMarketFundController extends Controller
{
    public function index()
    {
        return response()->json(MoneyMarketFund::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'value' => 'required|string|unique:money_market_funds,value',
            'label' => 'required|string',
            'return' => 'required|numeric',
        ]);
        $mmf = MoneyMarketFund::create($validated);
        return response()->json($mmf, 201);
    }

    public function update(Request $request, $id)
    {
        $mmf = MoneyMarketFund::findOrFail($id);
        $validated = $request->validate([
            'value' => 'required|string|unique:money_market_funds,value,' . $id,
            'label' => 'required|string',
            'return' => 'required|numeric',
        ]);
        $mmf->update($validated);
        return response()->json($mmf);
    }

    public function destroy($id)
    {
        $mmf = MoneyMarketFund::findOrFail($id);
        $mmf->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
