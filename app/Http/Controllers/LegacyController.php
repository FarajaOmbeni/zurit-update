<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\Beneficiary;
use App\Models\AssetBeneficiaryAllocation;
use App\Models\LegacyFiduciary;
use App\Models\Investment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            ->with(['beneficiaryAllocations.beneficiary'])
            ->orderBy('created_at', 'desc')
            ->get();

        $beneficiaries = Beneficiary::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('UserDashboard/Legacy/Assets', [
            'assets' => $legacyAssets,
            'beneficiaries' => $beneficiaries,
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

    public function updateAsset(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'value' => 'required|numeric|min:0',
            'acquisition_date' => 'nullable|date',
        ]);

        $asset = Asset::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('is_legacy', true)
            ->firstOrFail();

        $asset->update([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'value' => $request->value,
            'acquisition_date' => $request->acquisition_date,
        ]);

        return back()->with('success', 'Asset updated successfully');
    }

    public function destroyAsset($id)
    {
        $asset = Asset::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('is_legacy', true)
            ->firstOrFail();

        // Prevent deletion if allocations exist
        $existingAllocations = AssetBeneficiaryAllocation::where('asset_id', $id)->count();
        if ($existingAllocations > 0) {
            return back()->withErrors(['asset' => 'This asset has beneficiary allocations and cannot be deleted. Remove all allocations first.']);
        }

        $asset->delete();

        return back()->with('success', 'Asset deleted successfully');
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

    public function storeBeneficiary(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:20',
            'relationship' => 'nullable|string|max:100',
            'is_minor' => 'boolean',
            'email' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:255',
        ]);

        $beneficiary = Beneficiary::create([
            'user_id' => auth()->id(),
            'full_name' => $request->full_name,
            'national_id' => $request->national_id,
            'relationship' => $request->relationship,
            'is_minor' => $request->is_minor ?? false,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ]);

        return back()->with('success', 'Beneficiary added successfully');
    }

    public function updateBeneficiary(Request $request, $id)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:20',
            'relationship' => 'nullable|string|max:100',
            'is_minor' => 'boolean',
            'email' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:255',
        ]);

        $beneficiary = Beneficiary::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $beneficiary->update([
            'full_name' => $request->full_name,
            'national_id' => $request->national_id,
            'relationship' => $request->relationship,
            'is_minor' => $request->is_minor ?? false,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ]);

        return back()->with('success', 'Beneficiary updated successfully');
    }

    public function destroyBeneficiary($id)
    {
        $beneficiary = Beneficiary::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Prevent deletion if beneficiary is allocated to any asset
        $hasAllocations = AssetBeneficiaryAllocation::where('beneficiary_id', $id)->exists();
        if ($hasAllocations) {
            return back()->withErrors(['beneficiary' => 'This beneficiary has allocations and cannot be deleted. Remove their allocations first.']);
        }

        $beneficiary->delete();

        return back()->with('success', 'Beneficiary deleted successfully');
    }

    public function storeAssetAllocation(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'beneficiary_allocations' => 'required|array|min:1',
            'beneficiary_allocations.*.beneficiary_id' => 'required|exists:beneficiaries,id',
            'beneficiary_allocations.*.percentage' => 'required|numeric|min:0|max:100',
            'beneficiary_allocations.*.conditions' => 'nullable|string|max:500',
            'beneficiary_allocations.*.contingent_of' => 'nullable|exists:beneficiaries,id',
        ]);

        $user = auth()->user();
        $assetId = $request->asset_id;

        // Verify asset belongs to user
        $asset = Asset::where('id', $assetId)
            ->where('user_id', $user->id)
            ->where('is_legacy', true)
            ->firstOrFail();

        // Check if this is an update (existing allocations exist)
        $existingAllocations = AssetBeneficiaryAllocation::where('asset_id', $assetId)->count();
        $isUpdate = $existingAllocations > 0;

        // Validate beneficiary ownership (ensure all beneficiaries belong to the user)
        $beneficiaryIds = collect($request->beneficiary_allocations)->pluck('beneficiary_id')->unique();
        $userBeneficiaries = Beneficiary::where('user_id', $user->id)
            ->whereIn('id', $beneficiaryIds)
            ->count();

        if ($userBeneficiaries !== $beneficiaryIds->count()) {
            return back()->withErrors(['allocation' => 'One or more selected beneficiaries do not belong to you.']);
        }

        // Calculate total percentage for validation (allow up to 100%)
        $totalPercentage = collect($request->beneficiary_allocations)
            ->sum('percentage');

        if ($totalPercentage > 100.01) {
            return back()->withErrors(['allocation' => "Asset '{$asset->name}' allocations cannot exceed 100%. Current total: {$totalPercentage}%"]);
        }

        // Validate no duplicate beneficiaries for the same asset
        $duplicateBeneficiaries = collect($request->beneficiary_allocations)
            ->duplicates('beneficiary_id');

        if ($duplicateBeneficiaries->isNotEmpty()) {
            return back()->withErrors(['allocation' => 'Each beneficiary can only be allocated once per asset.']);
        }

        DB::transaction(function () use ($assetId, $request, $isUpdate) {
            // Clear existing allocations for this asset
            $deletedCount = AssetBeneficiaryAllocation::where('asset_id', $assetId)->delete();

            // Create new allocations
            foreach ($request->beneficiary_allocations as $allocation) {
                AssetBeneficiaryAllocation::create([
                    'asset_id' => $assetId,
                    'beneficiary_id' => $allocation['beneficiary_id'],
                    'percentage' => $allocation['percentage'],
                    'conditions' => $allocation['conditions'] ?? null,
                    'contingent_of' => $allocation['contingent_of'] ?? null,
                ]);
            }

            // Log the operation for audit purposes
            Log::info('Asset allocation ' . ($isUpdate ? 'updated' : 'created'), [
                'user_id' => auth()->id(),
                'asset_id' => $assetId,
                'is_update' => $isUpdate,
                'deleted_allocations' => $deletedCount,
                'new_allocations' => count($request->beneficiary_allocations)
            ]);
        });

        $message = $isUpdate
            ? 'Asset allocation updated successfully'
            : 'Asset allocation saved successfully';

        return back()->with('success', $message);
    }

    public function deleteAssetAllocation(Request $request, $allocation)
    {
        $user = auth()->user();

        // Find the allocation and verify ownership
        $allocationRecord = AssetBeneficiaryAllocation::where('id', $allocation)
            ->whereHas('asset', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('is_legacy', true);
            })
            ->firstOrFail();

        // Get asset and beneficiary names for logging
        $assetName = $allocationRecord->asset->name;
        $beneficiaryName = $allocationRecord->beneficiary->full_name;

        // Delete the allocation
        $allocationRecord->delete();

        // Log the operation
        Log::info('Asset allocation deleted', [
            'user_id' => $user->id,
            'allocation_id' => $allocation,
            'asset_name' => $assetName,
            'beneficiary_name' => $beneficiaryName
        ]);

        return response()->json([
            'success' => true,
            'message' => "Allocation for {$beneficiaryName} removed from {$assetName} successfully"
        ]);
    }

    public function getAssetAllocationStatus(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id'
        ]);

        $user = auth()->user();
        $assetId = $request->asset_id;

        // Verify asset belongs to user
        $asset = Asset::where('id', $assetId)
            ->where('user_id', $user->id)
            ->where('is_legacy', true)
            ->firstOrFail();

        $allocations = AssetBeneficiaryAllocation::where('asset_id', $assetId)
            ->with('beneficiary')
            ->get();

        $totalPercentage = $allocations->sum('percentage');
        $isComplete = abs($totalPercentage - 100) < 0.01;

        return response()->json([
            'has_allocations' => $allocations->count() > 0,
            'allocations_count' => $allocations->count(),
            'total_percentage' => $totalPercentage,
            'is_complete' => $isComplete,
            'allocations' => $allocations->map(function ($allocation) {
                return [
                    'id' => $allocation->id,
                    'beneficiary_id' => $allocation->beneficiary_id,
                    'beneficiary_name' => $allocation->beneficiary->full_name,
                    'percentage' => $allocation->percentage,
                    'conditions' => $allocation->conditions,
                    'contingent_of' => $allocation->contingent_of,
                ];
            })
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

                // Calculate total percentage for validation (allow up to 100%)
                $totalPercentage = collect($assetAllocation['beneficiary_allocations'])
                    ->sum('percentage');

                if ($totalPercentage > 100.01) {
                    throw new \Exception("Asset '{$asset->name}' allocations cannot exceed 100%. Current total: {$totalPercentage}%");
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

        return Inertia::render('UserDashboard/Legacy/Insurance', [
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
