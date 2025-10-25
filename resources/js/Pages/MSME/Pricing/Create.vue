<template>
    <Head title="Create Pricing Model" />
    <MSMESidebar title="Create Pricing Model">
        <div class="py-6">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                >
                    <div class="p-6">
                        <h1 class="text-3xl font-bold text-gray-900">
                            Create Pricing Model
                        </h1>
                        <p class="text-gray-600 mt-2">
                            Set up a comprehensive pricing model for your
                            product or service
                        </p>
                    </div>
                </div>

                <!-- Form -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submitForm" class="p-6 space-y-6">
                        <!-- Basic Information -->
                        <div>
                            <h3
                                class="text-lg font-semibold text-gray-900 mb-4"
                            >
                                Basic Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >Product/Service Name *</label
                                    >
                                    <input
                                        v-model="form.product_service_name"
                                        type="text"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                        placeholder="e.g., Custom Website Design"
                                    />
                                    <p
                                        v-if="form.errors.product_service_name"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.product_service_name }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >Type *</label
                                    >
                                    <select
                                        v-model="form.product_type"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                    >
                                        <option value="">Select Type</option>
                                        <option value="product">Product</option>
                                        <option value="service">Service</option>
                                    </select>
                                    <p
                                        v-if="form.errors.product_type"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.product_type }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >Industry Template</label
                                    >
                                    <select
                                        v-model="form.industry_template"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                    >
                                        <option value="">
                                            Select Template (Optional)
                                        </option>
                                        <option
                                            v-for="(template, key) in templates"
                                            :key="key"
                                            :value="key"
                                        >
                                            {{ template.name }}
                                        </option>
                                    </select>
                                    <button
                                        v-if="form.industry_template"
                                        @click="applyTemplate"
                                        type="button"
                                        class="mt-2 text-sm text-purple-600 hover:text-purple-800"
                                    >
                                        Apply Template Values
                                    </button>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >Market Positioning</label
                                    >
                                    <select
                                        v-model="form.market_positioning"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                    >
                                        <option value="">
                                            Select Positioning
                                        </option>
                                        <option value="premium">Premium</option>
                                        <option value="competitive">
                                            Competitive
                                        </option>
                                        <option value="budget">Budget</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Cost Structure -->
                        <div>
                            <h3
                                class="text-lg font-semibold text-gray-900 mb-4"
                            >
                                Cost Structure
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >Raw Material Cost *</label
                                    >
                                    <input
                                        v-model="form.raw_material_cost"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                        placeholder="0.00"
                                    />
                                    <p
                                        v-if="form.errors.raw_material_cost"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.raw_material_cost }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >Direct Labor Cost *</label
                                    >
                                    <input
                                        v-model="form.direct_labor_cost"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                        placeholder="0.00"
                                    />
                                    <p
                                        v-if="form.errors.direct_labor_cost"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.direct_labor_cost }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >Variable Overhead Cost *</label
                                    >
                                    <input
                                        v-model="form.variable_overhead_cost"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                        placeholder="0.00"
                                    />
                                    <p
                                        v-if="
                                            form.errors.variable_overhead_cost
                                        "
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.variable_overhead_cost }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >Fixed Overhead Cost *</label
                                    >
                                    <input
                                        v-model="form.fixed_overhead_cost"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                        placeholder="0.00"
                                    />
                                    <p
                                        v-if="form.errors.fixed_overhead_cost"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.fixed_overhead_cost }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing Strategy -->
                        <div>
                            <h3
                                class="text-lg font-semibold text-gray-900 mb-4"
                            >
                                Pricing Strategy
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >Desired Profit Margin (%) *</label
                                    >
                                    <input
                                        v-model="form.desired_profit_margin"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        max="100"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                        placeholder="25.00"
                                    />
                                    <p
                                        v-if="form.errors.desired_profit_margin"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.desired_profit_margin }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >Units per Period *</label
                                    >
                                    <input
                                        v-model="form.units_per_period"
                                        type="number"
                                        min="1"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                        placeholder="100"
                                    />
                                    <p
                                        v-if="form.errors.units_per_period"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.units_per_period }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >Period Type *</label
                                    >
                                    <select
                                        v-model="form.period_type"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                    >
                                        <option value="">Select Period</option>
                                        <option value="daily">Daily</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="yearly">Yearly</option>
                                    </select>
                                    <p
                                        v-if="form.errors.period_type"
                                        class="mt-1 text-sm text-red-600"
                                    >
                                        {{ form.errors.period_type }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >Seasonal Adjustment (%)</label
                                    >
                                    <input
                                        v-model="form.seasonal_adjustment"
                                        type="number"
                                        step="0.01"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                        placeholder="0.00"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Competitive Analysis -->
                        <div>
                            <h3
                                class="text-lg font-semibold text-gray-900 mb-4"
                            >
                                Competitive Analysis
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >Competitor Price (Low)</label
                                    >
                                    <input
                                        v-model="form.competitor_price_low"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                        placeholder="0.00"
                                    />
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >Competitor Price (High)</label
                                    >
                                    <input
                                        v-model="form.competitor_price_high"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                        placeholder="0.00"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div>
                            <h3
                                class="text-lg font-semibold text-gray-900 mb-4"
                            >
                                Additional Information
                            </h3>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                    >Notes</label
                                >
                                <textarea
                                    v-model="form.notes"
                                    rows="4"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                    placeholder="Any additional notes about this pricing model..."
                                ></textarea>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div
                            class="flex justify-end space-x-3 pt-6 border-t border-gray-200"
                        >
                            <Link
                                :href="route('pricing.index')"
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 bg-purple-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50"
                            >
                                <svg
                                    v-if="form.processing"
                                    class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>
                                {{
                                    form.processing
                                        ? "Creating..."
                                        : "Create Pricing Model"
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </MSMESidebar>
</template>

<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import MSMESidebar from "@/Components/MSMESidebar.vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    templates: Object,
});

const form = useForm({
    product_service_name: "",
    product_type: "",
    industry_template: "",
    raw_material_cost: 0,
    direct_labor_cost: 0,
    variable_overhead_cost: 0,
    fixed_overhead_cost: 0,
    desired_profit_margin: 25,
    competitor_price_low: null,
    competitor_price_high: null,
    market_positioning: "",
    units_per_period: 1,
    period_type: "monthly",
    seasonal_adjustment: 0,
    notes: "",
});

const applyTemplate = () => {
    if (form.industry_template) {
        router.post(
            route("pricing.apply-template"),
            {
                template: form.industry_template,
                business_size: "small", // Default to small business
            },
            {
                onSuccess: (page) => {
                    const templateData = page.props.templateData;
                    if (templateData) {
                        form.raw_material_cost = templateData.raw_material_cost;
                        form.direct_labor_cost = templateData.direct_labor_cost;
                        form.variable_overhead_cost =
                            templateData.variable_overhead_cost;
                        form.fixed_overhead_cost =
                            templateData.fixed_overhead_cost;
                        form.desired_profit_margin =
                            templateData.desired_profit_margin;
                    }
                },
            },
        );
    }
};

const submitForm = () => {
    form.post(route("pricing.store"), {
        onSuccess: () => {
            // Redirect will be handled by the controller
        },
    });
};
</script>
