<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ProfitLossRecord;
use App\Models\CashflowEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ProfitLossController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $profitLossRecords = ProfitLossRecord::where('user_id', $user->id)
            ->orderBy('period_end', 'desc')
            ->paginate(15);

        return Inertia::render('MSME/ProfitLoss/Index', [
            'profitLossRecords' => $profitLossRecords,
        ]);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = Auth::user();
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Generate P&L record from cashflow data
        $plRecord = ProfitLossRecord::generateFromCashflow($user->id, $startDate, $endDate);

        return redirect()->route('profit-loss.show', $plRecord)
            ->with('success', 'Profit & Loss statement generated successfully.');
    }

    public function show(ProfitLossRecord $profitLossRecord)
    {
        // Check ownership
        if ($profitLossRecord->user_id !== Auth::id()) {
            abort(403);
        }

        return Inertia::render('MSME/ProfitLoss/Show', [
            'profitLossRecord' => $profitLossRecord,
        ]);
    }

    public function update(Request $request, ProfitLossRecord $profitLossRecord)
    {
        // Check ownership
        if ($profitLossRecord->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $profitLossRecord->update($validated);

        return back()->with('success', 'Profit & Loss record updated successfully.');
    }

    public function download(ProfitLossRecord $profitLossRecord, $format)
    {
        // Check ownership
        if ($profitLossRecord->user_id !== Auth::id()) {
            abort(403);
        }

        // Validate format
        if (!in_array($format, ['pdf', 'excel'])) {
            abort(400, 'Invalid format. Only PDF and Excel are supported.');
        }

        if ($format === 'pdf') {
            return $this->generatePDF($profitLossRecord);
        } else {
            return $this->generateExcel($profitLossRecord);
        }
    }

    private function generatePDF(ProfitLossRecord $profitLossRecord)
    {
        // For now, return a simple text response
        // In production, you would use a PDF library like DomPDF or TCPDF
        $content = "Profit & Loss Statement\n";
        $content .= "Period: " . $profitLossRecord->period_start->format('Y-m-d') . " to " . $profitLossRecord->period_end->format('Y-m-d') . "\n\n";
        $content .= "Revenue: KES " . number_format((float)$profitLossRecord->revenue, 2) . "\n";
        $content .= "Cost of Goods Sold: KES " . number_format((float)$profitLossRecord->cost_of_goods_sold, 2) . "\n";
        $content .= "Gross Profit: KES " . number_format((float)$profitLossRecord->gross_profit, 2) . "\n";
        $content .= "Operating Expenses: KES " . number_format((float)$profitLossRecord->operating_expenses, 2) . "\n";
        $content .= "Tax Expense: KES " . number_format((float)$profitLossRecord->tax_expense, 2) . "\n";
        $content .= "Interest Expense: KES " . number_format((float)$profitLossRecord->interest_expense, 2) . "\n";
        $content .= "Net Profit: KES " . number_format((float)$profitLossRecord->net_profit, 2) . "\n";

        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="profit_loss_' . $profitLossRecord->id . '.txt"');
    }

    private function generateExcel(ProfitLossRecord $profitLossRecord)
    {
        // For now, return a simple CSV response
        // In production, you would use a library like PhpSpreadsheet
        $csv = "Profit & Loss Statement\n";
        $csv .= "Period," . $profitLossRecord->period_start->format('Y-m-d') . " to " . $profitLossRecord->period_end->format('Y-m-d') . "\n";
        $csv .= "Revenue," . $profitLossRecord->revenue . "\n";
        $csv .= "Cost of Goods Sold," . $profitLossRecord->cost_of_goods_sold . "\n";
        $csv .= "Gross Profit," . $profitLossRecord->gross_profit . "\n";
        $csv .= "Operating Expenses," . $profitLossRecord->operating_expenses . "\n";
        $csv .= "Tax Expense," . $profitLossRecord->tax_expense . "\n";
        $csv .= "Interest Expense," . $profitLossRecord->interest_expense . "\n";
        $csv .= "Net Profit," . $profitLossRecord->net_profit . "\n";

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="profit_loss_' . $profitLossRecord->id . '.csv"');
    }

    public function destroy(ProfitLossRecord $profitLossRecord)
    {
        // Check ownership
        if ($profitLossRecord->user_id !== Auth::id()) {
            abort(403);
        }

        $profitLossRecord->delete();

        return redirect()->route('profit-loss.index')
            ->with('success', 'Profit & Loss record deleted successfully.');
    }
}
