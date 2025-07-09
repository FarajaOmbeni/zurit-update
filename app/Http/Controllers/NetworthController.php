<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Debt;
use Inertia\Inertia;
use App\Models\Asset;
use App\Models\Liability;
use Illuminate\Http\Request;
use App\Mail\FinancialAssistance;
use App\Models\Investment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class NetworthController extends Controller
{
    public function index()
    {
        $assets = Asset::where('user_id', auth()->id())->get();

        // Get liabilities with selected columns
        $liabilities = Liability::where('user_id', auth()->id())
            ->select('name', 'amount', 'created_at', 'updated_at')
            ->get();

        $fixedIncomeTypes = ['mmf', 'bills', 'bonds', 'other'];
        $investments = Investment::where('user_id', auth()->id())
            ->select(
                'details_of_investment as name', // Alias to match frontend
                'type', // Select the 'type' column as it's used in the CASE statement
                DB::raw("CASE
                    WHEN type IN ('" . implode("','", $fixedIncomeTypes) . "') THEN current_amount
                    ELSE initial_amount
                 END AS value")
            )
            ->get();

        // Get debts with selected columns, renaming current_amount to amount
        $debts = Debt::where('user_id', auth()->id())
            ->select('name', DB::raw('initial_amount - current_amount as amount'), 'created_at', 'updated_at')
            ->get();

        // Combine the collectionsJ
        $combinedLiabilitiesAndDebts = $liabilities->concat($debts);

        return Inertia::render('UserDashboard/NetworthCalculator', [
            'assets' => $assets,
            'liabilities' => $combinedLiabilitiesAndDebts,
            'investments' => $investments
        ]);
    }

    public function storeAsset(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'value' => 'required|numeric',
            'acquisition_date' => 'nullable|date',
        ]);

        $asset = new Asset();
        $asset->user_id = auth()->id();
        $asset->name = $request->name;
        $asset->type = $request->type;
        $asset->description = $request->description;
        $asset->value = $request->value;

        // Set acquisition_date to today if not provided
        if ($request->acquisition_date) {
            $asset->acquisition_date = $request->acquisition_date;
        } else {
            $asset->acquisition_date = Carbon::now();
        }

        $asset->save();

        return to_route('networth.index');
    }

    public function storeLiability(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'amount' => 'required|numeric',
            'due_date' => 'nullable|date',
        ]);

        $liability = new Liability();
        $liability->user_id = auth()->id();
        $liability->name = $request->name;
        $liability->category = $request->category;
        $liability->description = $request->description;
        $liability->amount = $request->amount;

        if ($request->due_date) {
            $liability->due_date = $request->due_date;
        }

        $liability->save();

        return to_route('networth.index');
    }

    public function sendEmail(Request $request)
    {
        $user = Auth::user();
        $type = $request->type;

        try {
            Mail::to(config('services.email.admin_email'))->send(new FinancialAssistance($user, $type));
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroyAsset($id)
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

    public function destroyLiability($id)
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
