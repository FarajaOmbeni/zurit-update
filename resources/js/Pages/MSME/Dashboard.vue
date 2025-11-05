<template>
    <Head title="MSME Dashboard" />
    <MSMESidebar
        :title="
            businessProfile
                ? `${businessProfile.business_name} Dashboard`
                : 'MSME Dashboard'
        "
    >
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Welcome Header -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                >
                    <div class="p-6 text-gray-900">
                        <div
                            class="flex flex-col md:flex-row justify-between items-start md:items-center"
                        >
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900">
                                    {{
                                        businessProfile
                                            ? `${businessProfile.business_name} Dashboard`
                                            : "MSME Dashboard"
                                    }}
                                </h1>
                                <p class="text-gray-600 mt-2">
                                    {{
                                        businessProfile
                                            ? `${businessProfile.business_type} • ${businessProfile.industry_sector}`
                                            : "Manage your business finances and operations"
                                    }}
                                </p>

                                <!-- Quick Start Guide for New Users -->
                                <div
                                    v-if="!businessProfile"
                                    class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg"
                                >
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg
                                                class="h-5 w-5 text-yellow-600"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3
                                                class="text-sm font-medium text-yellow-800"
                                            >
                                                Getting Started
                                            </h3>
                                            <div
                                                class="mt-2 text-sm text-yellow-700"
                                            >
                                                <p>
                                                    Welcome! To get the most out
                                                    of your MSME dashboard:
                                                </p>
                                                <ol
                                                    class="list-decimal list-inside mt-2 space-y-1"
                                                >
                                                    <li>
                                                        Start by recording your
                                                        daily transactions
                                                    </li>
                                                    <li>
                                                        Set up your business
                                                        profile for better
                                                        insights
                                                    </li>
                                                    <li>
                                                        Generate your first
                                                        financial reports
                                                    </li>
                                                    <li>
                                                        Close your month-end to
                                                        see complete financial
                                                        statements
                                                    </li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 md:mt-0">
                                <Link
                                    v-if="!businessProfile"
                                    :href="route('msme.business-setup')"
                                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors"
                                >
                                    Complete Business Setup
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Health Indicators -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                >
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-4">
                            Business Health Indicators
                        </h2>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <HealthIndicator
                                title="Cash Flow"
                                :status="healthIndicators.cash_flow"
                                :value="
                                    formatCurrency(
                                        financialOverview.net_cash_position,
                                    )
                                "
                                :change="financialOverview.net_cash_growth"
                            />
                            <HealthIndicator
                                title="Profitability"
                                :status="healthIndicators.profitability"
                                :value="
                                    financialOverview.latest_profit_loss
                                        ? `${financialOverview.latest_profit_loss.net_profit_margin}%`
                                        : 'N/A'
                                "
                            />
                            <HealthIndicator
                                title="Liquidity"
                                :status="healthIndicators.liquidity"
                                :value="
                                    financialOverview.latest_balance_sheet &&
                                    typeof financialOverview
                                        .latest_balance_sheet.current_ratio ===
                                        'number'
                                        ? financialOverview.latest_balance_sheet.current_ratio.toFixed(
                                              2,
                                          )
                                        : 'N/A'
                                "
                            />
                            <HealthIndicator
                                title="Solvency"
                                :status="healthIndicators.solvency"
                                :value="
                                    financialOverview.latest_balance_sheet &&
                                    typeof financialOverview
                                        .latest_balance_sheet
                                        .debt_to_equity_ratio === 'number'
                                        ? financialOverview.latest_balance_sheet.debt_to_equity_ratio.toFixed(
                                              2,
                                          )
                                        : 'N/A'
                                "
                            />
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Row -->
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6"
                >
                    <!-- Net Cash Position -->
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
                                        Net Cash Position
                                    </p>
                                    <p
                                        class="text-2xl font-semibold text-gray-900"
                                    >
                                        {{
                                            formatCurrency(
                                                financialOverview.net_cash_position,
                                            )
                                        }}
                                    </p>
                                    <p
                                        :class="[
                                            'text-sm',
                                            financialOverview.net_cash_growth >=
                                            0
                                                ? 'text-green-600'
                                                : 'text-red-600',
                                        ]"
                                    >
                                        {{
                                            financialOverview.net_cash_growth >=
                                            0
                                                ? "+"
                                                : ""
                                        }}{{
                                            financialOverview.net_cash_growth?.toFixed(
                                                1,
                                            )
                                        }}% from last month
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Revenue -->
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
                                        Monthly Income
                                    </p>
                                    <p
                                        class="text-2xl font-semibold text-gray-900"
                                    >
                                        {{
                                            formatCurrency(
                                                financialOverview.current_income,
                                            )
                                        }}
                                    </p>
                                    <p
                                        :class="[
                                            'text-sm',
                                            financialOverview.income_growth >= 0
                                                ? 'text-green-600'
                                                : 'text-red-600',
                                        ]"
                                    >
                                        {{
                                            financialOverview.income_growth >= 0
                                                ? "+"
                                                : ""
                                        }}{{
                                            financialOverview.income_growth?.toFixed(
                                                1,
                                            )
                                        }}% growth
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Expenses -->
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
                                        Monthly Expenses
                                    </p>
                                    <p
                                        class="text-2xl font-semibold text-gray-900"
                                    >
                                        {{
                                            formatCurrency(
                                                financialOverview.current_expenses,
                                            )
                                        }}
                                    </p>
                                    <p
                                        :class="[
                                            'text-sm',
                                            financialOverview.expense_growth <=
                                            0
                                                ? 'text-green-600'
                                                : 'text-red-600',
                                        ]"
                                    >
                                        {{
                                            financialOverview.expense_growth >=
                                            0
                                                ? "+"
                                                : ""
                                        }}{{
                                            financialOverview.expense_growth?.toFixed(
                                                1,
                                            )
                                        }}% change
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Pricing Models -->
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
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
                                            ></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Pricing Models
                                    </p>
                                    <p
                                        class="text-2xl font-semibold text-gray-900"
                                    >
                                        {{ quickStats.total_pricing_models }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        {{ quickStats.average_profit_margin }}%
                                        avg margin
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                >
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">
                            Quick Actions
                        </h3>
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4"
                        >
                            <Link
                                :href="route('cashflow.index')"
                                class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                <div
                                    class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3"
                                >
                                    <svg
                                        class="w-5 h-5 text-green-600"
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
                                <div>
                                    <p class="font-medium text-gray-900">
                                        Cash Flow
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Track income & expenses
                                    </p>
                                </div>
                            </Link>
                            <Link
                                :href="route('month-end.index')"
                                class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                <div
                                    class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-3"
                                >
                                    <svg
                                        class="w-5 h-5 text-yellow-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 7V3m8 4V3M5 11h14M5 19h14m-7-8v8"
                                        ></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">
                                        Month-End Closing
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Post Closing Stock & Depreciation
                                    </p>
                                </div>
                            </Link>

                            <Link
                                :href="route('pricing.index')"
                                class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                <div
                                    class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3"
                                >
                                    <svg
                                        class="w-5 h-5 text-purple-600"
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
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">
                                        Pricing Models
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Set product prices
                                    </p>
                                </div>
                            </Link>

                            <Link
                                :href="route('msme.reports')"
                                class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                <div
                                    class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3"
                                >
                                    <svg
                                        class="w-5 h-5 text-blue-600"
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
                                <div>
                                    <p class="font-medium text-gray-900">
                                        Reports
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Generate P&L & Balance Sheet
                                    </p>
                                </div>
                            </Link>

                            <Link
                                :href="route('msme.business-setup')"
                                class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                <div
                                    class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-3"
                                >
                                    <svg
                                        class="w-5 h-5 text-yellow-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                                        ></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">
                                        Business Setup
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Complete your profile
                                    </p>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                >
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">
                            Recent Activities
                        </h3>
                        <div class="space-y-4">
                            <div
                                v-if="recentActivities.length === 0"
                                class="text-center py-8 text-gray-500"
                            >
                                <svg
                                    class="w-12 h-12 mx-auto mb-3 text-gray-300"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                                    ></path>
                                </svg>
                                <p>No recent activities</p>
                                <p class="text-sm">
                                    Start by recording your first transaction
                                </p>
                            </div>
                            <div
                                v-for="(activity, idx) in recentActivities"
                                :key="`${activity.type}-${activity.date}-${idx}`"
                                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                            >
                                <div class="flex items-center">
                                    <div
                                        :class="[
                                            'w-2 h-2 rounded-full mr-3',
                                            activity.type === 'cashflow'
                                                ? 'bg-green-500'
                                                : 'bg-purple-500',
                                        ]"
                                    ></div>
                                    <div>
                                        <p class="font-medium">
                                            {{ activity.description }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            {{ formatDate(activity.date) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold">
                                        {{ formatCurrency(activity.amount) }}
                                    </p>
                                    <p
                                        v-if="activity.category"
                                        class="text-sm text-gray-600"
                                    >
                                        {{ activity.category }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Glossary -->
                <FinancialGlossary />
            </div>
        </div>
    </MSMESidebar>
</template>

<script setup>
import { Head, Link } from "@inertiajs/vue3";
import MSMESidebar from "@/Components/MSMESidebar.vue";
import HealthIndicator from "@/Components/MSME/HealthIndicator.vue";
import FinancialGlossary from "@/Components/Shared/FinancialGlossary.vue";
import { useFormatCurrency } from "@/Components/Composables/useFormatCurrency";
import { formatDate } from "@/Components/Composables/useDateFormat";

const props = defineProps({
    businessProfile: Object,
    financialOverview: Object,
    healthIndicators: Object,
    recentActivities: Array,
    quickStats: Object,
});

const { formatCurrency } = useFormatCurrency();
</script>
