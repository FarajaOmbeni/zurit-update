<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\Asset;
use App\Models\Liability;
use Illuminate\Http\Request;
use App\Mail\FinancialAssistance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class NetworthController extends Controller
{
    //
    public function storeAsset(Request $request)
    {
        $userId = Auth::id();
        // Validate the form data
        $validatedData = $request->validate([
            'assetDescription' => 'required',
            'assetValue' => 'required|numeric',
        ]);

        // Store the asset in the database
        $asset = Asset::create([
            'user_id' => $userId,
            'asset_description' => $validatedData['assetDescription'],
            'asset_value' => $validatedData['assetValue'],
        ]);

        // Redirect or perform any other action after storing
        return redirect('user_networthcalc')->with('success', [
            'message' => 'Assest Added Succesfully',
            'duration' => 3000,
        ]);
    }

    public function storeLiability(Request $request)
    {
        $userId = Auth::id();
        // Validate the form data
        $validatedData = $request->validate([
            'liabilityDescription' => 'required',
            'liabilityValue' => 'required|numeric',
        ]);

        // Store the liability in the database
        $liability = Debt::create([
            'user_id' => $userId,
            'debt_name' => $validatedData['liabilityDescription'],
            'current_balance' => $validatedData['liabilityValue'],
        ]);

        return redirect('user_networthcalc')->with('success', [
            'message' => 'Liability Added Succesfully',
            'duration' => 3000,
        ]);
    }

    public function showNetWorth()
    {
        $assets = Asset::where('user_id', auth()->id())->get();
        $liabilities = Debt::where('user_id', auth()->id())->get();
        $liabilities2 = Liability::where('user_id', auth()->id())->get();

        // Calculate current month's totals
        $currentMonth = now();
        $currentMonthAssets = Asset::where('user_id', auth()->id())
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->sum('asset_value');

        $currentMonthLiabilities = Debt::where('user_id', auth()->id())
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->sum('current_balance');

        $currentMonthMinPayments = Debt::where('user_id', auth()->id())
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->sum('minimum_payment');

        // Get historical net worth data for the last 6 months
        $historicalNetWorthData = [];
        $historicalMonths = [];
        $currentMonth = now()->startOfMonth();

        for ($i = 5; $i >= 0; $i--) {
            $month = $currentMonth->copy()->subMonths($i);

            // Get assets created in this specific month
            $totalAssets = Asset::where('user_id', auth()->id())
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('asset_value');

            // Get liabilities created in this specific month
            $totalLiabilities = Debt::where('user_id', auth()->id())
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('current_balance');

            $totalMinPayments = Debt::where('user_id', auth()->id())
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('minimum_payment');

            // Calculate net worth for this month
            $netWorth = $totalAssets - ($totalLiabilities - $totalMinPayments);

            $historicalNetWorthData[] = $netWorth;
            $historicalMonths[] = $month->format('M Y');
        }
        $debts = Debt::where('user_id', auth()->id())
            ->whereColumn('current_balance', '!=', 'minimum_payment')
            ->get();

        return view('user_networthcalc', [
            'assets' => $assets,
            'debts' => $debts,
            'liabilities' => $liabilities,
            'liabilities2' => $liabilities2,
            'historicalNetWorthData' => $historicalNetWorthData,
            'historicalMonths' => $historicalMonths,
            'currentMonthAssets' => $currentMonthAssets,
            'currentMonthLiabilities' => $currentMonthLiabilities - $currentMonthMinPayments,
        ]);
    }

    public function sendEmail(Request $request)
    {
        $user = Auth::user();
        $type = $request->type;

        try {
            Mail::to('info@zuritconsulting.com')->send(new FinancialAssistance($user, $type));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroy_asset($id)
    {
        $asset = Asset::find($id);

        if ($asset) {
            $asset->delete();
            return redirect('user_networthcalc')->with('success', [
                'message' => 'Asset deleted successfully!',
                'duration' => 3000,
            ]);
        }

        return redirect('user_networthcalc')->with('error', [
            'message' => 'Error deleting asset',
            'duration' => 3000,
        ]);
    }

    public function destroy_liability($id)
    {
        $liability = Liability::find($id);

        if ($liability) {
            $liability->delete();
            return redirect('user_networthcalc')->with('success', [
                'message' => 'liability deleted successfully!',
                'duration' => 3000,
            ]);
        }

        return redirect('user_networthcalc')->with('error', [
            'message' => 'Error deleting liability',
            'duration' => 3000,
        ]);
    }
}
