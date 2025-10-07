<template>
    <Head title="Cashflow Analytics" />
    <MSMESidebar title="Cashflow Analytics">
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
                                    Cashflow Analytics
                                </h1>
                                <p class="text-gray-600 mt-2">
                                    Comprehensive analysis of your cash flow
                                    patterns and trends
                                </p>
                            </div>
                            <div class="mt-4 md:mt-0 flex space-x-3">
                                <Link
                                    :href="route('cashflow.index')"
                                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors"
                                >
                                    Back to Cashflow
                                </Link>
                                <select
                                    v-model="selectedPeriod"
                                    @change="updatePeriod"
                                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors border-0"
                                >
                                    <option value="1m">Last Month</option>
                                    <option value="3m">Last 3 Months</option>
                                    <option value="6m">Last 6 Months</option>
                                    <option value="12m">Last 12 Months</option>
                                </select>
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
                                        Total Income
                                    </p>
                                    <p
                                        class="text-2xl font-semibold text-gray-900"
                                    >
                                        {{ formatCurrency(totalIncome) }}
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
                                        class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center"
                                    >
                                        <svg
                                            class="w-4 h-4 text-red-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 14l-7 7m0 0l-7-7m7 7V3"
                                            ></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Total Expenses
                                    </p>
                                    <p
                                        class="text-2xl font-semibold text-gray-900"
                                    >
                                        {{ formatCurrency(totalExpenses) }}
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
                                        :class="[
                                            'w-8 h-8 rounded-full flex items-center justify-center',
                                            netCashflow >= 0
                                                ? 'bg-green-100'
                                                : 'bg-red-100',
                                        ]"
                                    >
                                        <svg
                                            :class="[
                                                'w-4 h-4',
                                                netCashflow >= 0
                                                    ? 'text-green-600'
                                                    : 'text-red-600',
                                            ]"
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
                                        Net Cashflow
                                    </p>
                                    <p
                                        :class="[
                                            'text-2xl font-semibold',
                                            netCashflow >= 0
                                                ? 'text-green-600'
                                                : 'text-red-600',
                                        ]"
                                    >
                                        {{ formatCurrency(netCashflow) }}
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
                                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
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
                                        {{ profitMargin.toFixed(1) }}%
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Trends Chart -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                >
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Monthly Cashflow Trends
                        </h3>
                        <div class="h-64 flex items-end space-x-2">
                            <div
                                v-for="(trend, index) in monthlyTrends"
                                :key="index"
                                class="flex-1 flex flex-col items-center"
                            >
                                <div
                                    class="w-full flex flex-col items-center space-y-1"
                                >
                                    <!-- Income Bar -->
                                    <div
                                        class="w-full bg-green-200 rounded-t"
                                        :style="{
                                            height:
                                                getBarHeight(
                                                    trend.income,
                                                    maxAmount,
                                                ) + 'px',
                                        }"
                                        :title="`Income: ${formatCurrency(trend.income)}`"
                                    ></div>
                                    <!-- Expenses Bar -->
                                    <div
                                        class="w-full bg-red-200 rounded-b"
                                        :style="{
                                            height:
                                                getBarHeight(
                                                    trend.expenses,
                                                    maxAmount,
                                                ) + 'px',
                                        }"
                                        :title="`Expenses: ${formatCurrency(trend.expenses)}`"
                                    ></div>
                                </div>
                                <div
                                    class="text-xs text-gray-600 mt-2 text-center"
                                >
                                    {{ trend.month_name }}
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-center space-x-6 mt-4">
                            <div class="flex items-center">
                                <div
                                    class="w-4 h-4 bg-green-200 rounded mr-2"
                                ></div>
                                <span class="text-sm text-gray-600"
                                    >Income</span
                                >
                            </div>
                            <div class="flex items-center">
                                <div
                                    class="w-4 h-4 bg-red-200 rounded mr-2"
                                ></div>
                                <span class="text-sm text-gray-600"
                                    >Expenses</span
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Breakdown -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Income Categories -->
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <h3
                                class="text-lg font-semibold text-gray-900 mb-4"
                            >
                                Income by Category
                            </h3>
                            <div
                                v-if="
                                    Object.keys(categoryBreakdown.income)
                                        .length > 0
                                "
                                class="space-y-3"
                            >
                                <div
                                    v-for="(
                                        amount, category
                                    ) in categoryBreakdown.income"
                                    :key="category"
                                    class="flex items-center justify-between"
                                >
                                    <span
                                        class="text-sm text-gray-600 capitalize"
                                        >{{
                                            formatCategoryName(category)
                                        }}</span
                                    >
                                    <div class="flex items-center space-x-2">
                                        <div
                                            class="w-24 bg-gray-200 rounded-full h-2"
                                        >
                                            <div
                                                class="bg-green-500 h-2 rounded-full"
                                                :style="{
                                                    width:
                                                        getPercentage(
                                                            amount,
                                                            totalIncome,
                                                        ) + '%',
                                                }"
                                            ></div>
                                        </div>
                                        <span
                                            class="text-sm font-medium text-gray-900 w-20 text-right"
                                        >
                                            {{ formatCurrency(amount) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-8 text-gray-500">
                                No income data for this period
                            </div>
                        </div>
                    </div>

                    <!-- Expense Categories -->
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <h3
                                class="text-lg font-semibold text-gray-900 mb-4"
                            >
                                Expenses by Category
                            </h3>
                            <div
                                v-if="
                                    Object.keys(categoryBreakdown.expenses)
                                        .length > 0
                                "
                                class="space-y-3"
                            >
                                <div
                                    v-for="(
                                        amount, category
                                    ) in categoryBreakdown.expenses"
                                    :key="category"
                                    class="flex items-center justify-between"
                                >
                                    <span
                                        class="text-sm text-gray-600 capitalize"
                                        >{{
                                            formatCategoryName(category)
                                        }}</span
                                    >
                                    <div class="flex items-center space-x-2">
                                        <div
                                            class="w-24 bg-gray-200 rounded-full h-2"
                                        >
                                            <div
                                                class="bg-red-500 h-2 rounded-full"
                                                :style="{
                                                    width:
                                                        getPercentage(
                                                            amount,
                                                            totalExpenses,
                                                        ) + '%',
                                                }"
                                            ></div>
                                        </div>
                                        <span
                                            class="text-sm font-medium text-gray-900 w-20 text-right"
                                        >
                                            {{ formatCurrency(amount) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-8 text-gray-500">
                                No expense data for this period
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Analysis -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                >
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Payment Method Analysis
                        </h3>
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"
                        >
                            <div
                                v-for="(
                                    method, paymentMethod
                                ) in paymentMethodAnalysis"
                                :key="paymentMethod"
                                class="border border-gray-200 rounded-lg p-4"
                            >
                                <div
                                    class="flex items-center justify-between mb-2"
                                >
                                    <span
                                        class="text-sm font-medium text-gray-900 capitalize"
                                    >
                                        {{
                                            formatPaymentMethodName(
                                                paymentMethod,
                                            )
                                        }}
                                    </span>
                                    <span class="text-xs text-gray-500"
                                        >{{ method.count }} transactions</span
                                    >
                                </div>
                                <div
                                    class="text-lg font-semibold text-gray-900"
                                >
                                    {{ formatCurrency(method.total) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{
                                        getPercentage(
                                            method.total,
                                            totalAmount,
                                        )
                                    }}% of total
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cashflow Forecast -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            3-Month Cashflow Forecast
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div
                                v-for="(forecast, index) in forecast"
                                :key="index"
                                class="border border-gray-200 rounded-lg p-4"
                            >
                                <div
                                    class="text-sm font-medium text-gray-900 mb-2"
                                >
                                    {{ forecast.month_name }}
                                </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600"
                                            >Projected Income:</span
                                        >
                                        <span
                                            class="font-medium text-green-600"
                                        >
                                            {{
                                                formatCurrency(
                                                    forecast.projected_income,
                                                )
                                            }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600"
                                            >Projected Expenses:</span
                                        >
                                        <span class="font-medium text-red-600">
                                            {{
                                                formatCurrency(
                                                    forecast.projected_expenses,
                                                )
                                            }}
                                        </span>
                                    </div>
                                    <div
                                        class="flex justify-between text-sm border-t pt-2"
                                    >
                                        <span class="font-medium text-gray-900"
                                            >Net Projection:</span
                                        >
                                        <span
                                            :class="[
                                                'font-medium',
                                                forecast.projected_net >= 0
                                                    ? 'text-green-600'
                                                    : 'text-red-600',
                                            ]"
                                        >
                                            {{
                                                formatCurrency(
                                                    forecast.projected_net,
                                                )
                                            }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 text-xs text-gray-500">
                            * Forecast is based on historical averages from the
                            last 6 months
                        </div>
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
import { ref, computed } from "vue";

const props = defineProps({
    monthlyTrends: Array,
    categoryBreakdown: Object,
    paymentMethodAnalysis: Object,
    forecast: Array,
    period: String,
});

const { formatCurrency } = useFormatCurrency();

const selectedPeriod = ref(props.period);

// Computed properties for key metrics
const totalIncome = computed(() => {
    return props.monthlyTrends.reduce((sum, trend) => sum + trend.income, 0);
});

const totalExpenses = computed(() => {
    return props.monthlyTrends.reduce((sum, trend) => sum + trend.expenses, 0);
});

const netCashflow = computed(() => {
    return totalIncome.value - totalExpenses.value;
});

const profitMargin = computed(() => {
    return totalIncome.value > 0
        ? (netCashflow.value / totalIncome.value) * 100
        : 0;
});

const totalAmount = computed(() => {
    return totalIncome.value + totalExpenses.value;
});

const maxAmount = computed(() => {
    const maxIncome = Math.max(...props.monthlyTrends.map((t) => t.income));
    const maxExpenses = Math.max(...props.monthlyTrends.map((t) => t.expenses));
    return Math.max(maxIncome, maxExpenses);
});

// Helper functions
const updatePeriod = () => {
    window.location.href = route("cashflow.analytics", {
        period: selectedPeriod.value,
    });
};

const getBarHeight = (amount, maxAmount) => {
    if (maxAmount === 0) return 0;
    return Math.max((amount / maxAmount) * 200, 2); // Minimum 2px height
};

const getPercentage = (value, total) => {
    if (total === 0) return 0;
    return ((value / total) * 100).toFixed(1);
};

const formatCategoryName = (category) => {
    return category.replace(/_/g, " ").replace(/\b\w/g, (l) => l.toUpperCase());
};

const formatPaymentMethodName = (method) => {
    return method.replace(/_/g, " ").replace(/\b\w/g, (l) => l.toUpperCase());
};
</script>
