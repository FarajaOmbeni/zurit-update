<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\BalanceSheetRecord;
use App\Models\Asset;
use App\Models\Liability;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BalanceSheetController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $balanceSheetRecords = BalanceSheetRecord::where('user_id', $user->id)
            ->orderBy('as_of_date', 'desc')
            ->paginate(15);

        return Inertia::render('MSME/BalanceSheet/Index', [
            'balanceSheetRecords' => $balanceSheetRecords,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'as_of_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        
        // Generate balance sheet from assets and liabilities
        $balanceSheetRecord = BalanceSheetRecord::generateFromAssetsAndLiabilities($user->id, Carbon::parse($validated['as_of_date']));
        
        if ($validated['notes']) {
            $balanceSheetRecord->update(['notes' => $validated['notes']]);
        }

        return redirect()->route('balance-sheet.show', $balanceSheetRecord)
            ->with('success', 'Balance sheet generated successfully.');
    }

    public function show(BalanceSheetRecord $balanceSheetRecord)
    {
        // Check ownership
        if ($balanceSheetRecord->user_id !== Auth::id()) {
            abort(403);
        }

        return Inertia::render('MSME/BalanceSheet/Show', [
            'balanceSheetRecord' => $balanceSheetRecord,
        ]);
    }

    public function update(Request $request, BalanceSheetRecord $balanceSheetRecord)
    {
        // Check ownership
        if ($balanceSheetRecord->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $balanceSheetRecord->update($validated);

        return back()->with('success', 'Balance sheet record updated successfully.');
    }

    public function destroy(BalanceSheetRecord $balanceSheetRecord)
    {
        // Check ownership
        if ($balanceSheetRecord->user_id !== Auth::id()) {
            abort(403);
        }

        $balanceSheetRecord->delete();

        return redirect()->route('balance-sheet.index')
            ->with('success', 'Balance sheet record deleted successfully.');
    }

    public function download(BalanceSheetRecord $balanceSheetRecord, $format)
    {
        // Check ownership
        if ($balanceSheetRecord->user_id !== Auth::id()) {
            abort(403);
        }

        // Validate format
        if (!in_array($format, ['pdf', 'excel'])) {
            abort(400, 'Invalid format. Only PDF and Excel are supported.');
        }

        if ($format === 'pdf') {
            return $this->generatePDF($balanceSheetRecord);
        }

        return $this->generateExcel($balanceSheetRecord);
    }

    private function generatePDF(BalanceSheetRecord $balanceSheetRecord)
    {
        // Placeholder plain text export to keep dependencies light
        $content = "Balance Sheet\n";
        $content .= "As of: " . $balanceSheetRecord->as_of_date->format('Y-m-d') . "\n\n";
        $content .= "Total Assets: KES " . number_format((float)$balanceSheetRecord->total_assets, 2) . "\n";
        $content .= "Total Liabilities: KES " . number_format((float)$balanceSheetRecord->total_liabilities, 2) . "\n";
        $content .= "Equity: KES " . number_format((float)$balanceSheetRecord->equity, 2) . "\n";
        $content .= "Current Ratio: " . number_format((float)($balanceSheetRecord->current_ratio ?? 0), 2) . "\n";
        $content .= "Debt to Equity: " . number_format((float)($balanceSheetRecord->debt_to_equity_ratio ?? 0), 2) . "\n";

        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="balance_sheet_' . $balanceSheetRecord->id . '.txt"');
    }

    private function generateExcel(BalanceSheetRecord $balanceSheetRecord)
    {
        // Simple CSV export
        $csv = "Balance Sheet\n";
        $csv .= "As of," . $balanceSheetRecord->as_of_date->format('Y-m-d') . "\n";
        $csv .= "Total Assets," . $balanceSheetRecord->total_assets . "\n";
        $csv .= "Total Liabilities," . $balanceSheetRecord->total_liabilities . "\n";
        $csv .= "Equity," . $balanceSheetRecord->equity . "\n";
        $csv .= "Current Ratio," . ($balanceSheetRecord->current_ratio ?? 0) . "\n";
        $csv .= "Debt to Equity," . ($balanceSheetRecord->debt_to_equity_ratio ?? 0) . "\n";

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="balance_sheet_' . $balanceSheetRecord->id . '.csv"');
    }
}
