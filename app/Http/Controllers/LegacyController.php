<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\Beneficiary;
use App\Models\AssetBeneficiaryAllocation;
use App\Models\LegacyFiduciary;
use App\Models\Investment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class LegacyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'subscribed']);
    }

    /**
     * Step 1: Assets management
     */
    public function assets()
    {
        $user = auth()->user();
        $legacyAssets = Asset::where('user_id', $user->id)
            ->where('is_legacy', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('UserDashboard/Legacy/Assets', [
            'assets' => $legacyAssets
        ]);
    }

    public function storeAsset(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'value' => 'required|numeric|min:0',
            'acquisition_date' => 'nullable|date',
        ]);

        Asset::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'value' => $request->value,
            'acquisition_date' => $request->acquisition_date,
            'is_legacy' => true,
        ]);

        return back()->with('success', 'Asset added successfully');
    }

    /**
     * Step 2: Beneficiaries and allocations
     */
    public function beneficiaries()
    {
        $user = auth()->user();

        $beneficiaries = Beneficiary::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $legacyAssets = Asset::where('user_id', $user->id)
            ->where('is_legacy', true)
            ->with('beneficiaryAllocations.beneficiary')
            ->get();

        return Inertia::render('UserDashboard/Legacy/Beneficiaries', [
            'beneficiaries' => $beneficiaries,
            'assets' => $legacyAssets
        ]);
    }

    public function saveAllocations(Request $request)
    {
        $request->validate([
            'beneficiaries' => 'required|array',
            'beneficiaries.*.full_name' => 'required|string|max:255',
            'beneficiaries.*.national_id' => 'nullable|string|max:20',
            'beneficiaries.*.relationship' => 'nullable|string|max:100',
            'beneficiaries.*.is_minor' => 'boolean',
            'beneficiaries.*.contact' => 'nullable|string|max:255',
            'allocations' => 'required|array',
            'allocations.*.asset_id' => 'required|exists:assets,id',
            'allocations.*.beneficiary_allocations' => 'required|array',
            'allocations.*.beneficiary_allocations.*.percentage' => 'required|numeric|min:0|max:100',
            'allocations.*.beneficiary_allocations.*.conditions' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $user = auth()->user();

            // First, save/update beneficiaries
            $beneficiaryMap = [];
            foreach ($request->beneficiaries as $beneficiaryData) {
                $beneficiary = Beneficiary::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'full_name' => $beneficiaryData['full_name'],
                    ],
                    $beneficiaryData + ['user_id' => $user->id]
                );
                $beneficiaryMap[$beneficiaryData['temp_id'] ?? $beneficiary->id] = $beneficiary->id;
            }

            // Then, save allocations
            foreach ($request->allocations as $assetAllocation) {
                $assetId = $assetAllocation['asset_id'];

                // Verify asset belongs to user
                $asset = Asset::where('id', $assetId)
                    ->where('user_id', $user->id)
                    ->where('is_legacy', true)
                    ->firstOrFail();

                // Calculate total percentage for validation
                $totalPercentage = collect($assetAllocation['beneficiary_allocations'])
                    ->sum('percentage');

                if (abs($totalPercentage - 100) > 0.01) {
                    throw new \Exception("Asset '{$asset->name}' allocations must total 100%. Current total: {$totalPercentage}%");
                }

                // Clear existing allocations for this asset
                AssetBeneficiaryAllocation::where('asset_id', $assetId)->delete();

                // Create new allocations
                foreach ($assetAllocation['beneficiary_allocations'] as $allocation) {
                    $beneficiaryId = $beneficiaryMap[$allocation['beneficiary_temp_id']] ?? $allocation['beneficiary_id'];

                    AssetBeneficiaryAllocation::create([
                        'asset_id' => $assetId,
                        'beneficiary_id' => $beneficiaryId,
                        'percentage' => $allocation['percentage'],
                        'conditions' => $allocation['conditions'],
                        'contingent_of' => $allocation['contingent_of'] ?? null,
                    ]);
                }
            }
        });

        return back()->with('success', 'Beneficiaries and allocations saved successfully');
    }

    /**
     * Step 3: Fiduciaries
     */
    public function fiduciaries()
    {
        $user = auth()->user();
        $fiduciaries = LegacyFiduciary::where('user_id', $user->id)->first();

        return Inertia::render('UserDashboard/Legacy/Fiduciaries', [
            'fiduciaries' => $fiduciaries
        ]);
    }

    public function saveFiduciaries(Request $request)
    {
        $request->validate([
            'executors' => 'nullable|array',
            'trustees' => 'nullable|array',
            'guardians' => 'nullable|array',
            'witness_placeholders' => 'nullable|array',
        ]);

        LegacyFiduciary::updateOrCreate(
            ['user_id' => auth()->id()],
            $request->only(['executors', 'trustees', 'guardians', 'witness_placeholders'])
        );

        return back()->with('success', 'Fiduciaries saved successfully');
    }

    /**
     * Step 4: Insurance audit
     */
    public function insurance()
    {
        $user = auth()->user();
        $insuranceInvestments = Investment::where('user_id', $user->id)
            ->whereIn('type', ['insurance', 'pension'])
            ->get();

        return Inertia::render('UserDashboard/Legacy/InsuranceAudit', [
            'insuranceInvestments' => $insuranceInvestments
        ]);
    }

    public function saveInsurance(Request $request)
    {
        // This could be used to update insurance beneficiaries or add reminders
        // For now, we'll just acknowledge the audit
        return back()->with('success', 'Insurance audit completed');
    }

    /**
     * Step 5: Review and generate
     */
    public function review()
    {
        $user = auth()->user();

        $legacyAssets = Asset::where('user_id', $user->id)
            ->where('is_legacy', true)
            ->with('beneficiaryAllocations.beneficiary')
            ->get();

        $beneficiaries = Beneficiary::where('user_id', $user->id)->get();
        $fiduciaries = LegacyFiduciary::where('user_id', $user->id)->first();
        $insuranceInvestments = Investment::where('user_id', $user->id)
            ->whereIn('type', ['insurance', 'pension'])
            ->get();

        return Inertia::render('UserDashboard/Legacy/Review', [
            'assets' => $legacyAssets,
            'beneficiaries' => $beneficiaries,
            'fiduciaries' => $fiduciaries,
            'insuranceInvestments' => $insuranceInvestments
        ]);
    }

    public function generate(Request $request)
    {
        // TODO: Implement PDF generation
        return back()->with('success', 'Legacy documents generation will be implemented in the next phase');
    }
}
