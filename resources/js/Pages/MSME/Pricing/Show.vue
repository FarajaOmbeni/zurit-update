<template>
    <Head title="Pricing Model Details" />
    <MSMESidebar title="Pricing Model Details">
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                >
                    <div class="p-6">
                        <div
                            class="flex flex-col md:flex-row justify-between items-start md:items-center"
                        >
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900">
                                    {{ pricingModel.product_service_name }}
                                </h1>
                                <p class="text-gray-600 mt-2 capitalize">
                                    {{ pricingModel.product_type }} •
                                    {{
                                        pricingModel.industry_template ||
                                        "No template"
                                    }}
                                </p>
                            </div>
                            <div class="mt-4 md:mt-0 flex space-x-3">
                                <Link
                                    :href="
                                        route('pricing.edit', pricingModel.id)
                                    "
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
                                >
                                    Edit Model
                                </Link>
                                <Link
                                    :href="
                                        route('pricing.export', pricingModel.id)
                                    "
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors"
                                >
                                    Export Analysis
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Key Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center"
                                    >
                                        <svg
                                            class="w-4 h-4 text-green-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"
                                            ></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Suggested Price
                                    </p>
                                    <p
                                        class="text-2xl font-semibold text-gray-900"
                                    >
                                        {{
                                            formatCurrency(
                                                pricingModel.suggested_selling_price,
                                            )
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center"
                                    >
                                        <svg
                                            class="w-4 h-4 text-blue-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                            ></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Profit Margin
                                    </p>
                                    <p
                                        class="text-2xl font-semibold text-gray-900"
                                    >
                                        {{
                                            pricingModel.desired_profit_margin
                                        }}%
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center"
                                    >
                                        <svg
                                            class="w-4 h-4 text-purple-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                                            ></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Break-even Qty
                                    </p>
                                    <p
                                        class="text-2xl font-semibold text-gray-900"
                                    >
                                        {{
                                            Math.round(
                                                pricingModel.break_even_quantity,
                                            )
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center"
                                    >
                                        <svg
                                            class="w-4 h-4 text-yellow-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"
                                            ></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Monthly Units
                                    </p>
                                    <p
                                        class="text-2xl font-semibold text-gray-900"
                                    >
                                        {{ pricingModel.units_per_period }}
                                        {{ pricingModel.period_type }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cost Breakdown -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                >
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Cost Breakdown
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600"
                                        >Raw Material Cost:</span
                                    >
                                    <span class="font-semibold">{{
                                        formatCurrency(
                                            pricingModel.raw_material_cost,
                                        )
                                    }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600"
                                        >Direct Labor Cost:</span
                                    >
                                    <span class="font-semibold">{{
                                        formatCurrency(
                                            pricingModel.direct_labor_cost,
                                        )
                                    }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600"
                                        >Variable Overhead:</span
                                    >
                                    <span class="font-semibold">{{
                                        formatCurrency(
                                            pricingModel.variable_overhead_cost,
                                        )
                                    }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600"
                                        >Fixed Overhead:</span
                                    >
                                    <span class="font-semibold">{{
                                        formatCurrency(
                                            pricingModel.fixed_overhead_cost,
                                        )
                                    }}</span>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="flex justify-between border-t pt-2">
                                    <span
                                        class="text-sm font-medium text-gray-900"
                                        >Total Cost per Unit:</span
                                    >
                                    <span class="font-bold text-lg">{{
                                        formatCurrency(
                                            pricingModel.total_cost_per_unit,
                                        )
                                    }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600"
                                        >Markup Percentage:</span
                                    >
                                    <span class="font-semibold"
                                        >{{
                                            pricingModel.markup_percentage
                                        }}%</span
                                    >
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600"
                                        >Break-even Revenue:</span
                                    >
                                    <span class="font-semibold">{{
                                        formatCurrency(
                                            pricingModel.break_even_revenue,
                                        )
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Competitive Analysis -->
                <div
                    v-if="
                        pricingModel.competitor_price_low ||
                        pricingModel.competitor_price_high
                    "
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                >
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Competitive Analysis
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div
                                v-if="pricingModel.competitor_price_low"
                                class="text-center"
                            >
                                <p class="text-sm text-gray-600">
                                    Lowest Competitor
                                </p>
                                <p class="text-2xl font-semibold text-red-600">
                                    {{
                                        formatCurrency(
                                            pricingModel.competitor_price_low,
                                        )
                                    }}
                                </p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-600">Your Price</p>
                                <p
                                    class="text-2xl font-semibold text-green-600"
                                >
                                    {{
                                        formatCurrency(
                                            pricingModel.suggested_selling_price,
                                        )
                                    }}
                                </p>
                            </div>
                            <div
                                v-if="pricingModel.competitor_price_high"
                                class="text-center"
                            >
                                <p class="text-sm text-gray-600">
                                    Highest Competitor
                                </p>
                                <p class="text-2xl font-semibold text-blue-600">
                                    {{
                                        formatCurrency(
                                            pricingModel.competitor_price_high,
                                        )
                                    }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <span
                                :class="[
                                    'px-3 py-1 rounded-full text-sm font-medium',
                                    competitivePosition === 'competitive'
                                        ? 'bg-green-100 text-green-800'
                                        : competitivePosition === 'below_market'
                                          ? 'bg-yellow-100 text-yellow-800'
                                          : competitivePosition ===
                                              'above_market'
                                            ? 'bg-red-100 text-red-800'
                                            : 'bg-gray-100 text-gray-800',
                                ]"
                            >
                                {{
                                    competitivePosition === "competitive"
                                        ? "Competitive"
                                        : competitivePosition === "below_market"
                                          ? "Below Market"
                                          : competitivePosition ===
                                              "above_market"
                                            ? "Above Market"
                                            : "Unknown"
                                }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Sensitivity Analysis -->
                <div
                    v-if="sensitivityAnalysis && sensitivityAnalysis.length > 0"
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                >
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Sensitivity Analysis
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Price Change
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            New Price
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Contribution Margin
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Break-even Qty
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Profit Margin
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-gray-200"
                                >
                                    <tr
                                        v-for="analysis in sensitivityAnalysis"
                                        :key="analysis.price_change_percent"
                                    >
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                                        >
                                            <span
                                                :class="[
                                                    analysis.price_change_percent >
                                                    0
                                                        ? 'text-green-600'
                                                        : analysis.price_change_percent <
                                                            0
                                                          ? 'text-red-600'
                                                          : 'text-gray-900',
                                                ]"
                                            >
                                                {{
                                                    analysis.price_change_percent >
                                                    0
                                                        ? "+"
                                                        : ""
                                                }}{{
                                                    analysis.price_change_percent
                                                }}%
                                            </span>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        >
                                            {{
                                                formatCurrency(
                                                    analysis.new_price,
                                                )
                                            }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        >
                                            {{
                                                formatCurrency(
                                                    analysis.contribution_margin,
                                                )
                                            }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        >
                                            {{
                                                Math.round(
                                                    analysis.break_even_quantity,
                                                )
                                            }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        >
                                            {{
                                                analysis.profit_margin_percent.toFixed(
                                                    1,
                                                )
                                            }}%
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div
                    v-if="pricingModel.notes"
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Notes
                        </h3>
                        <p class="text-gray-700 whitespace-pre-wrap">
                            {{ pricingModel.notes }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </MSMESidebar>
</template>

<script setup>
import { Head, Link } from "@inertiajs/vue3";
import MSMESidebar from "@/Components/MSMESidebar.vue";
import { useFormatCurrency } from "@/Components/Composables/useFormatCurrency";

const props = defineProps({
    pricingModel: Object,
    sensitivityAnalysis: Array,
    competitivePosition: String,
});

const { formatCurrency } = useFormatCurrency();
</script>
