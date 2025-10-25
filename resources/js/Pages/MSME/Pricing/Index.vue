<template>
    <Head title="Pricing Models" />
    <MSMESidebar title="Pricing Models">
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
                                    Pricing Models
                                </h1>
                                <p class="text-gray-600 mt-2">
                                    Set optimal prices for your products and
                                    services
                                </p>
                            </div>
                            <div class="mt-4 md:mt-0 flex space-x-3">
                                <Link
                                    :href="route('pricing.create')"
                                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors"
                                >
                                    Create Pricing Model
                                </Link>
                                <button
                                    @click="openCalculator"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
                                >
                                    Quick Calculator
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing Models Grid -->
                <div
                    v-if="pricingModels.data.length > 0"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6"
                >
                    <div
                        v-for="model in pricingModels.data"
                        :key="model.id"
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow"
                    >
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3
                                        class="text-lg font-semibold text-gray-900"
                                    >
                                        {{ model.product_service_name }}
                                    </h3>
                                    <p class="text-sm text-gray-600 capitalize">
                                        {{ model.product_type }}
                                    </p>
                                    <p
                                        v-if="model.industry_template"
                                        class="text-xs text-purple-600 capitalize"
                                    >
                                        {{ model.industry_template }}
                                    </p>
                                </div>
                                <div class="flex space-x-2">
                                    <Link
                                        :href="route('pricing.show', model.id)"
                                        class="text-blue-600 hover:text-blue-800 text-sm"
                                    >
                                        View
                                    </Link>
                                    <Link
                                        :href="route('pricing.edit', model.id)"
                                        class="text-green-600 hover:text-green-800 text-sm"
                                    >
                                        Edit
                                    </Link>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600"
                                        >Suggested Price:</span
                                    >
                                    <span class="font-semibold text-lg">{{
                                        formatCurrency(
                                            model.suggested_selling_price,
                                        )
                                    }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600"
                                        >Profit Margin:</span
                                    >
                                    <span class="font-medium"
                                        >{{
                                            model.desired_profit_margin
                                        }}%</span
                                    >
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600"
                                        >Break-even Qty:</span
                                    >
                                    <span class="font-medium">{{
                                        Math.round(model.break_even_quantity)
                                    }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600"
                                        >Monthly Units:</span
                                    >
                                    <span class="font-medium"
                                        >{{ model.units_per_period }}
                                        {{ model.period_type }}</span
                                    >
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600"
                                        >Status:</span
                                    >
                                    <span
                                        :class="[
                                            'px-2 py-1 rounded-full text-xs font-medium',
                                            model.is_active
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-gray-100 text-gray-800',
                                        ]"
                                    >
                                        {{
                                            model.is_active
                                                ? "Active"
                                                : "Inactive"
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-else
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="p-12 text-center">
                        <svg
                            class="w-16 h-16 mx-auto mb-4 text-gray-300"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
                            ></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            No Pricing Models
                        </h3>
                        <p class="text-gray-600 mb-6">
                            Create your first pricing model to start optimizing
                            your product and service prices.
                        </p>
                        <Link
                            :href="route('pricing.create')"
                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors"
                        >
                            Create Your First Pricing Model
                        </Link>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="pricingModels.last_page > 1" class="mt-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ pricingModels.from }} to
                            {{ pricingModels.to }} of
                            {{ pricingModels.total }} results
                        </div>
                        <div class="flex space-x-1">
                            <Link
                                v-for="link in pricingModels.links"
                                :key="link.label"
                                :href="link.url"
                                v-html="link.label"
                                :class="[
                                    'px-3 py-2 text-sm border rounded',
                                    link.active
                                        ? 'bg-purple-600 text-white border-purple-600'
                                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50',
                                ]"
                            >
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Calculator Modal -->
        <div
            v-if="showCalculator"
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div
                class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
            >
                <div
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    @click="closeCalculator"
                ></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                    >&#8203;</span
                >
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                >
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3
                            class="text-lg leading-6 font-medium text-gray-900 mb-4"
                        >
                            Quick Pricing Calculator
                        </h3>
                        <form
                            @submit.prevent="calculatePricing"
                            class="space-y-4"
                        >
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                    >Raw Material Cost</label
                                >
                                <input
                                    v-model="calculatorForm.raw_material_cost"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                />
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                    >Direct Labor Cost</label
                                >
                                <input
                                    v-model="calculatorForm.direct_labor_cost"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                />
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                    >Variable Overhead</label
                                >
                                <input
                                    v-model="
                                        calculatorForm.variable_overhead_cost
                                    "
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                />
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                    >Fixed Overhead</label
                                >
                                <input
                                    v-model="calculatorForm.fixed_overhead_cost"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                />
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                    >Desired Profit Margin (%)</label
                                >
                                <input
                                    v-model="
                                        calculatorForm.desired_profit_margin
                                    "
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                />
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                    >Units per Period</label
                                >
                                <input
                                    v-model="calculatorForm.units_per_period"
                                    type="number"
                                    min="1"
                                    required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                />
                            </div>
                        </form>

                        <!-- Results -->
                        <div
                            v-if="calculationResults"
                            class="mt-6 p-4 bg-gray-50 rounded-lg"
                        >
                            <h4 class="font-semibold text-gray-900 mb-3">
                                Calculation Results
                            </h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600"
                                        >Total Cost per Unit:</span
                                    >
                                    <span class="font-semibold">{{
                                        formatCurrency(
                                            calculationResults.total_cost_per_unit,
                                        )
                                    }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600"
                                        >Suggested Price:</span
                                    >
                                    <span
                                        class="font-semibold text-lg text-green-600"
                                        >{{
                                            formatCurrency(
                                                calculationResults.suggested_selling_price,
                                            )
                                        }}</span
                                    >
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600"
                                        >Markup %:</span
                                    >
                                    <span class="font-semibold"
                                        >{{
                                            calculationResults.markup_percentage
                                        }}%</span
                                    >
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600"
                                        >Break-even Quantity:</span
                                    >
                                    <span class="font-semibold">{{
                                        Math.round(
                                            calculationResults.break_even_quantity,
                                        )
                                    }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600"
                                        >Projected Monthly Profit:</span
                                    >
                                    <span
                                        class="font-semibold text-green-600"
                                        >{{
                                            formatCurrency(
                                                calculationResults.projected_monthly_profit,
                                            )
                                        }}</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"
                    >
                        <button
                            @click="closeCalculator"
                            type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </MSMESidebar>
</template>

<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import MSMESidebar from "@/Components/MSMESidebar.vue";
import { useFormatCurrency } from "@/Components/Composables/useFormatCurrency";
import { ref, reactive } from "vue";

const props = defineProps({
    pricingModels: Object,
    templates: Object,
});

const { formatCurrency } = useFormatCurrency();

const showCalculator = ref(false);
const calculationResults = ref(null);

const calculatorForm = reactive({
    raw_material_cost: 0,
    direct_labor_cost: 0,
    variable_overhead_cost: 0,
    fixed_overhead_cost: 0,
    desired_profit_margin: 25,
    units_per_period: 1,
});

const openCalculator = () => {
    showCalculator.value = true;
    calculationResults.value = null;
};

const closeCalculator = () => {
    showCalculator.value = false;
    calculationResults.value = null;
};

const calculatePricing = () => {
    router.post(route("pricing.calculator"), calculatorForm, {
        onSuccess: (page) => {
            calculationResults.value = page.props.calculationResults;
        },
        onError: (errors) => {
            console.error("Calculation error:", errors);
        },
    });
};
</script>
