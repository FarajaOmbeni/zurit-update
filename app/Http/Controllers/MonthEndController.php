<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\CashflowEntry;
use App\Models\MonthEndClosure;
use App\Models\ProfitLossRecord;
use App\Models\BalanceSheetRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MonthEndController extends Controller
{
    public function index()
    {
        // Simple page to submit closing stock for the current/selected month
        return Inertia::render('MSME/MonthEnd');
    }

    public function close(Request $request)
    {
        $request->validate([
            'period_end' => 'required|date',
            'closing_stock_value' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $periodEnd = Carbon::parse($request->period_end)->endOfMonth();
        $periodStart = $periodEnd->copy()->startOfMonth();

        // Compute opening stock from previous closure
        $prev = MonthEndClosure::where('user_id', $user->id)
            ->where('period_end', '<', $periodStart)
            ->orderBy('period_end', 'desc')
            ->first();
        $opening = $prev?->closing_stock_value ?? 0;

        // Sum purchases from cashflow entries categorized as inventory within period
        $purchases = CashflowEntry::where('user_id', $user->id)
            ->where('type', 'expense')
            ->where('category', 'inventory')
            ->whereBetween('entry_date', [$periodStart, $periodEnd])
            ->sum('amount');

        $closing = (float) $request->closing_stock_value;
        $cogs = max(0, ($opening + $purchases) - $closing);

        // Upsert month-end closure
        $closure = MonthEndClosure::updateOrCreate(
            [
                'user_id' => $user->id,
                'period_start' => $periodStart->toDateString(),
                'period_end' => $periodEnd->toDateString(),
            ],
            [
                'opening_stock_value' => $opening,
                'purchases_total' => $purchases,
                'closing_stock_value' => $closing,
                'calculated_cogs' => $cogs,
                'closed_at' => now(),
                'locked' => true,
            ]
        );

        // Update Inventory asset to reflect closing stock value
        Asset::updateOrCreate(
            [
                'user_id' => $user->id,
                'asset_type' => 'inventory',
                'name' => 'Inventory',
            ],
            [
                'type' => 'inventory',
                'current_value' => $closing,
                'value' => $closing,
                'date_acquired' => $periodEnd->toDateString(),
                'acquisition_date' => $periodEnd->toDateString(),
            ]
        );

        // Post monthly depreciation (straight-line) for depreciable assets
        $depreciationTotal = 0;
        $assets = Asset::where('user_id', $user->id)->where('is_depreciable', true)->get();
        foreach ($assets as $asset) {
            $monthly = $asset->getMonthlyDepreciationAmount();
            if ($monthly > 0) {
                // Do not depreciate below residual value
                $maxAccum = max(0, (float)$asset->value - (float)($asset->residual_value ?? 0));
                $newAccum = min($maxAccum, (float)$asset->depreciation + $monthly);
                $posted = $newAccum - (float)$asset->depreciation;
                if ($posted > 0.0) {
                    $asset->depreciation = $newAccum;
                    $asset->save();
                    $depreciationTotal += $posted;
                }
            }
        }

        if ($depreciationTotal > 0) {
            $closure->depreciation_posted = true;
            $closure->save();
        }

        // Generate or update Profit & Loss for the month
        $pl = ProfitLossRecord::where('user_id', $user->id)
            ->where('period_start', $periodStart)
            ->where('period_end', $periodEnd)
            ->first();
        if (!$pl) {
            $pl = ProfitLossRecord::generateFromCashflow($user->id, $periodStart, $periodEnd);
        } else {
            // Update COGS and depreciation if record exists
            $pl->cost_of_goods_sold = $cogs;
            $pl->depreciation = ($pl->depreciation ?? 0) + $depreciationTotal;
            $pl->gross_profit = $pl->revenue - $pl->cost_of_goods_sold;
            $pl->net_profit = $pl->revenue - ($pl->cost_of_goods_sold + $pl->operating_expenses + $pl->tax_expense + $pl->interest_expense + ($pl->depreciation ?? 0));
            $pl->save();
        }

        // Generate balance sheet as of period end
        BalanceSheetRecord::generateFromAssetsAndLiabilities($user->id, $periodEnd);

        return back()->with('success', 'Month-end closed. COGS and depreciation posted.');
    }
}

