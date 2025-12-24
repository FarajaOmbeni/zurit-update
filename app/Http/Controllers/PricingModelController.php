<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\PricingModel;
use Illuminate\Support\Facades\Auth;

class PricingModelController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $pricingModels = PricingModel::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $templates = $this->getIndustryTemplates();
        
        return Inertia::render('MSME/Pricing/Index', [
            'pricingModels' => $pricingModels,
            'templates' => $templates,
        ]);
    }

    public function create()
    {
        $templates = $this->getIndustryTemplates();
        
        return Inertia::render('MSME/Pricing/Create', [
            'templates' => $templates,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_service_name' => 'required|string|max:255',
            'product_type' => 'required|in:product,service',
            'industry_template' => 'nullable|string',
            'raw_material_cost' => 'required|numeric|min:0',
            'direct_labor_cost' => 'required|numeric|min:0',
            'variable_overhead_cost' => 'required|numeric|min:0',
            'fixed_overhead_cost' => 'required|numeric|min:0',
            'desired_profit_margin' => 'required|numeric|min:0|max:100',
            'competitor_price_low' => 'nullable|numeric|min:0',
            'competitor_price_high' => 'nullable|numeric|min:0',
            'market_positioning' => 'nullable|in:premium,competitive,budget',
            'units_per_period' => 'required|integer|min:1',
            'period_type' => 'required|in:daily,weekly,monthly,yearly',
            'seasonal_adjustment' => 'nullable|numeric',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Convert empty strings to null for nullable fields
        $validated['industry_template'] = $validated['industry_template'] === '' ? null : $validated['industry_template'];
        $validated['market_positioning'] = $validated['market_positioning'] === '' ? null : $validated['market_positioning'];
        $validated['competitor_price_low'] = $validated['competitor_price_low'] === '' || $validated['competitor_price_low'] === null ? null : $validated['competitor_price_low'];
        $validated['competitor_price_high'] = $validated['competitor_price_high'] === '' || $validated['competitor_price_high'] === null ? null : $validated['competitor_price_high'];
        $validated['notes'] = $validated['notes'] === '' ? null : $validated['notes'];
        $validated['seasonal_adjustment'] = $validated['seasonal_adjustment'] === '' || $validated['seasonal_adjustment'] === null ? 0 : $validated['seasonal_adjustment'];

        $validated['user_id'] = Auth::id();

        $pricingModel = PricingModel::create($validated);

        return redirect()->route('pricing.show', $pricingModel)
            ->with('success', 'Pricing model created successfully.');
    }

    public function show(PricingModel $pricingModel)
    {
        // Check ownership
        if ($pricingModel->user_id !== Auth::id()) {
            abort(403);
        }

        // Generate sensitivity analysis
        $sensitivityAnalysis = $pricingModel->sensitivityAnalysis();
        
        // Get competitive analysis
        $competitivePosition = $pricingModel->competitive_position;
        
        return Inertia::render('MSME/Pricing/Show', [
            'pricingModel' => $pricingModel,
            'sensitivityAnalysis' => $sensitivityAnalysis,
            'competitivePosition' => $competitivePosition,
        ]);
    }

    public function edit(PricingModel $pricingModel)
    {
        // Check ownership
        if ($pricingModel->user_id !== Auth::id()) {
            abort(403);
        }

        $templates = $this->getIndustryTemplates();
        
        return Inertia::render('MSME/Pricing/Edit', [
            'pricingModel' => $pricingModel,
            'templates' => $templates,
        ]);
    }

    public function update(Request $request, PricingModel $pricingModel)
    {
        // Check ownership
        if ($pricingModel->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'product_service_name' => 'required|string|max:255',
            'product_type' => 'required|in:product,service',
            'industry_template' => 'nullable|string',
            'raw_material_cost' => 'required|numeric|min:0',
            'direct_labor_cost' => 'required|numeric|min:0',
            'variable_overhead_cost' => 'required|numeric|min:0',
            'fixed_overhead_cost' => 'required|numeric|min:0',
            'desired_profit_margin' => 'required|numeric|min:0|max:100',
            'competitor_price_low' => 'nullable|numeric|min:0',
            'competitor_price_high' => 'nullable|numeric|min:0',
            'market_positioning' => 'nullable|in:premium,competitive,budget',
            'units_per_period' => 'required|integer|min:1',
            'period_type' => 'required|in:daily,weekly,monthly,yearly',
            'seasonal_adjustment' => 'nullable|numeric',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        // Convert empty strings to null for nullable fields
        $validated['industry_template'] = $validated['industry_template'] === '' ? null : $validated['industry_template'];
        $validated['market_positioning'] = $validated['market_positioning'] === '' ? null : $validated['market_positioning'];
        $validated['competitor_price_low'] = $validated['competitor_price_low'] === '' || $validated['competitor_price_low'] === null ? null : $validated['competitor_price_low'];
        $validated['competitor_price_high'] = $validated['competitor_price_high'] === '' || $validated['competitor_price_high'] === null ? null : $validated['competitor_price_high'];
        $validated['notes'] = $validated['notes'] === '' ? null : $validated['notes'];
        $validated['seasonal_adjustment'] = $validated['seasonal_adjustment'] === '' || $validated['seasonal_adjustment'] === null ? 0 : $validated['seasonal_adjustment'];

        $pricingModel->update($validated);

        return redirect()->route('pricing.show', $pricingModel)
            ->with('success', 'Pricing model updated successfully.');
    }

    public function destroy(PricingModel $pricingModel)
    {
        // Check ownership
        if ($pricingModel->user_id !== Auth::id()) {
            abort(403);
        }

        $pricingModel->delete();

        return redirect()->route('pricing.index')
            ->with('success', 'Pricing model deleted successfully.');
    }

    public function calculator(Request $request)
    {
        // Quick pricing calculator without saving
        $validated = $request->validate([
            'raw_material_cost' => 'required|numeric|min:0',
            'direct_labor_cost' => 'required|numeric|min:0',
            'variable_overhead_cost' => 'required|numeric|min:0',
            'fixed_overhead_cost' => 'required|numeric|min:0',
            'desired_profit_margin' => 'required|numeric|min:0|max:100',
            'units_per_period' => 'required|integer|min:1',
        ]);

        // Calculate pricing
        $totalCostPerUnit = $validated['raw_material_cost'] + 
                           $validated['direct_labor_cost'] + 
                           $validated['variable_overhead_cost'] + 
                           $validated['fixed_overhead_cost'];

        $suggestedPrice = $totalCostPerUnit / (1 - ($validated['desired_profit_margin'] / 100));
        
        $markupPercentage = $totalCostPerUnit > 0 ? 
            (($suggestedPrice - $totalCostPerUnit) / $totalCostPerUnit) * 100 : 0;

        // Break-even calculation
        $variableCostPerUnit = $validated['raw_material_cost'] + 
                              $validated['direct_labor_cost'] + 
                              $validated['variable_overhead_cost'];
        $contributionMargin = $suggestedPrice - $variableCostPerUnit;
        $breakEvenQuantity = $contributionMargin > 0 ? 
            $validated['fixed_overhead_cost'] / $contributionMargin : 0;

        return response()->json([
            'total_cost_per_unit' => round($totalCostPerUnit, 2),
            'suggested_selling_price' => round($suggestedPrice, 2),
            'markup_percentage' => round($markupPercentage, 2),
            'break_even_quantity' => round($breakEvenQuantity, 2),
            'break_even_revenue' => round($breakEvenQuantity * $suggestedPrice, 2),
            'projected_monthly_profit' => round(($validated['units_per_period'] * ($suggestedPrice - $totalCostPerUnit)), 2),
        ]);
    }

    public function applyTemplate(Request $request)
    {
        $request->validate([
            'template' => 'required|string',
            'business_size' => 'required|in:micro,small,medium',
        ]);

        $templateData = $this->getTemplateData($request->template, $request->business_size);
        
        return response()->json($templateData);
    }

    public function exportAnalysis(PricingModel $pricingModel, Request $request)
    {
        // Check ownership
        if ($pricingModel->user_id !== Auth::id()) {
            abort(403);
        }

        $format = $request->get('format', 'pdf'); // pdf, excel
        
        $analysisData = [
            'pricing_model' => $pricingModel,
            'sensitivity_analysis' => $pricingModel->sensitivityAnalysis(),
            'cost_breakdown' => [
                'raw_materials' => $pricingModel->raw_material_cost,
                'direct_labor' => $pricingModel->direct_labor_cost,
                'variable_overhead' => $pricingModel->variable_overhead_cost,
                'fixed_overhead' => $pricingModel->fixed_overhead_cost,
            ],
            'pricing_metrics' => [
                'suggested_price' => $pricingModel->suggested_selling_price,
                'profit_margin' => $pricingModel->desired_profit_margin,
                'markup_percentage' => $pricingModel->markup_percentage,
                'break_even_quantity' => $pricingModel->break_even_quantity,
                'break_even_revenue' => $pricingModel->break_even_revenue,
            ],
        ];

        // For now, return JSON data
        // In production, this would generate actual PDF/Excel files
        return response()->json([
            'format' => $format,
            'data' => $analysisData,
            'generated_at' => now()->toISOString(),
        ]);
    }

    private function getIndustryTemplates()
    {
        return [
            'manufacturing' => [
                'name' => 'Manufacturing',
                'description' => 'For businesses that produce physical goods',
                'typical_costs' => [
                    'raw_materials' => 40,
                    'direct_labor' => 25,
                    'variable_overhead' => 15,
                    'fixed_overhead' => 20,
                ],
                'suggested_margin' => 25,
            ],
            'retail' => [
                'name' => 'Retail',
                'description' => 'For businesses that sell products to consumers',
                'typical_costs' => [
                    'raw_materials' => 60, // Cost of goods
                    'direct_labor' => 15,
                    'variable_overhead' => 10,
                    'fixed_overhead' => 15,
                ],
                'suggested_margin' => 30,
            ],
            'service' => [
                'name' => 'Service',
                'description' => 'For service-based businesses',
                'typical_costs' => [
                    'raw_materials' => 10, // Materials/supplies
                    'direct_labor' => 50,
                    'variable_overhead' => 15,
                    'fixed_overhead' => 25,
                ],
                'suggested_margin' => 35,
            ],
            'agriculture' => [
                'name' => 'Agriculture',
                'description' => 'For farming and agricultural businesses',
                'typical_costs' => [
                    'raw_materials' => 35, // Seeds, fertilizer, etc.
                    'direct_labor' => 30,
                    'variable_overhead' => 20,
                    'fixed_overhead' => 15,
                ],
                'suggested_margin' => 20,
            ],
            'technology' => [
                'name' => 'Technology',
                'description' => 'For software and tech services',
                'typical_costs' => [
                    'raw_materials' => 5, // Software licenses, etc.
                    'direct_labor' => 60,
                    'variable_overhead' => 15,
                    'fixed_overhead' => 20,
                ],
                'suggested_margin' => 40,
            ],
            'food_beverage' => [
                'name' => 'Food & Beverage',
                'description' => 'For restaurants and food production',
                'typical_costs' => [
                    'raw_materials' => 30, // Food ingredients
                    'direct_labor' => 35,
                    'variable_overhead' => 15,
                    'fixed_overhead' => 20,
                ],
                'suggested_margin' => 25,
            ],
        ];
    }

    private function getTemplateData($template, $businessSize)
    {
        $templates = $this->getIndustryTemplates();
        $templateData = $templates[$template] ?? null;
        
        if (!$templateData) {
            return null;
        }

        // Adjust for business size
        $sizeMultipliers = [
            'micro' => 0.8,
            'small' => 1.0,
            'medium' => 1.3,
        ];

        $multiplier = $sizeMultipliers[$businessSize] ?? 1.0;
        
        // Sample base costs (would be more sophisticated in production)
        $baseCosts = [
            'manufacturing' => 100,
            'retail' => 80,
            'service' => 50,
            'agriculture' => 120,
            'technology' => 60,
            'food_beverage' => 90,
        ];

        $baseCost = $baseCosts[$template] ?? 100;
        $adjustedCost = $baseCost * $multiplier;

        return [
            'template_name' => $templateData['name'],
            'raw_material_cost' => round(($adjustedCost * $templateData['typical_costs']['raw_materials']) / 100, 2),
            'direct_labor_cost' => round(($adjustedCost * $templateData['typical_costs']['direct_labor']) / 100, 2),
            'variable_overhead_cost' => round(($adjustedCost * $templateData['typical_costs']['variable_overhead']) / 100, 2),
            'fixed_overhead_cost' => round(($adjustedCost * $templateData['typical_costs']['fixed_overhead']) / 100, 2),
            'desired_profit_margin' => $templateData['suggested_margin'],
            'industry_template' => $template,
        ];
    }

    public function compare(Request $request)
    {
        $request->validate([
            'model_ids' => 'required|array|min:2|max:5',
            'model_ids.*' => 'exists:pricing_models,id',
        ]);

        $user = Auth::user();
        
        $models = PricingModel::where('user_id', $user->id)
            ->whereIn('id', $request->model_ids)
            ->get();

        // Generate comparison data
        $comparison = $models->map(function ($model) {
            return [
                'id' => $model->id,
                'name' => $model->product_service_name,
                'type' => $model->product_type,
                'total_cost' => $model->total_cost_per_unit,
                'suggested_price' => $model->suggested_selling_price,
                'profit_margin' => $model->desired_profit_margin,
                'markup_percentage' => $model->markup_percentage,
                'break_even_quantity' => $model->break_even_quantity,
                'competitive_position' => $model->competitive_position,
                'projected_monthly_profit' => $model->projected_monthly_profit,
            ];
        });

        return Inertia::render('MSME/Pricing/Compare', [
            'models' => $comparison,
        ]);
    }
}
