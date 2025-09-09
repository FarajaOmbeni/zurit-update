<template>
    <Head title="MSME Dashboard" />
    <AuthenticatedLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Welcome Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900">
                                    {{ businessProfile ? `${businessProfile.business_name} Dashboard` : 'Business Dashboard' }}
                                </h1>
                                <p class="text-gray-600 mt-2">
                                    {{ businessProfile ? `${businessProfile.business_type} • ${businessProfile.industry_sector}` : 'Manage your business finances and operations' }}
                                </p>
                            </div>
                            <div class="mt-4 md:mt-0">
                                <Link v-if="!businessProfile" 
                                      :href="route('msme.business-setup')"
                                      class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                                    Complete Business Setup
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Health Indicators -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-4">Business Health Indicators</h2>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <HealthIndicator 
                                title="Cash Flow"
                                :status="healthIndicators.cash_flow"
                                :value="formatCurrency(financialOverview.net_cash_position)"
                                :change="financialOverview.net_cash_growth"
                            />
                            <HealthIndicator 
                                title="Profitability"
                                :status="healthIndicators.profitability"
                                :value="financialOverview.latest_profit_loss ? `${financialOverview.latest_profit_loss.net_profit_margin}%` : 'N/A'"
                            />
                            <HealthIndicator 
                                title="Liquidity"
                                :status="healthIndicators.liquidity"
                                :value="financialOverview.latest_balance_sheet ? financialOverview.latest_balance_sheet.current_ratio : 'N/A'"
                            />
                            <HealthIndicator 
                                title="Solvency"
                                :status="healthIndicators.solvency"
                                :value="financialOverview.latest_balance_sheet ? financialOverview.latest_balance_sheet.debt_to_equity_ratio : 'N/A'"
                            />
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Net Cash Position -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Net Cash Position</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(financialOverview.net_cash_position) }}</p>
                                    <p :class="[
                                        'text-sm',
                                        financialOverview.net_cash_growth >= 0 ? 'text-green-600' : 'text-red-600'
                                    ]">
                                        {{ financialOverview.net_cash_growth >= 0 ? '+' : '' }}{{ financialOverview.net_cash_growth?.toFixed(1) }}% from last month
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Revenue -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Monthly Income</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(financialOverview.current_income) }}</p>
                                    <p :class="[
                                        'text-sm',
                                        financialOverview.income_growth >= 0 ? 'text-green-600' : 'text-red-600'
                                    ]">
                                        {{ financialOverview.income_growth >= 0 ? '+' : '' }}{{ financialOverview.income_growth?.toFixed(1) }}% growth
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Expenses -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Monthly Expenses</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(financialOverview.current_expenses) }}</p>
                                    <p :class="[
                                        'text-sm',
                                        financialOverview.expense_growth <= 0 ? 'text-green-600' : 'text-red-600'
                                    ]">
                                        {{ financialOverview.expense_growth >= 0 ? '+' : '' }}{{ financialOverview.expense_growth?.toFixed(1) }}% change
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Pricing Models -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Pricing Models</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ quickStats.total_pricing_models }}</p>
                                    <p class="text-sm text-gray-600">{{ quickStats.average_profit_margin }}% avg margin</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Quick Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <Link :href="route('cashflow.index')" 
                                      class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium">Record Transaction</p>
                                        <p class="text-sm text-gray-600">Add income or expense</p>
                                    </div>
                                </Link>

                                <Link :href="route('pricing.create')" 
                                      class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium">Price Product/Service</p>
                                        <p class="text-sm text-gray-600">Calculate optimal pricing</p>
                                    </div>
                                </Link>

                                <Link :href="route('msme.reports')" 
                                      class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium">Generate Report</p>
                                        <p class="text-sm text-gray-600">P&L, Balance Sheet, Cashflow</p>
                                    </div>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Recent Activities</h3>
                            <div class="space-y-4">
                                <div v-if="recentActivities.length === 0" class="text-center py-8 text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                    </svg>
                                    <p>No recent activities</p>
                                    <p class="text-sm">Start by recording your first transaction</p>
                                </div>
                                <div v-for="activity in recentActivities" :key="activity.date" 
                                     class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div :class="[
                                            'w-2 h-2 rounded-full mr-3',
                                            activity.type === 'cashflow' ? 'bg-green-500' : 'bg-purple-500'
                                        ]"></div>
                                        <div>
                                            <p class="font-medium">{{ activity.description }}</p>
                                            <p class="text-sm text-gray-600">{{ formatDate(activity.date) }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold">{{ formatCurrency(activity.amount) }}</p>
                                        <p v-if="activity.category" class="text-sm text-gray-600">{{ activity.category }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import HealthIndicator from '@/Components/MSME/HealthIndicator.vue';
import { useFormatCurrency } from '@/Components/Composables/useFormatCurrency';
import { formatDate } from '@/Components/Composables/useDateFormat';

const props = defineProps({
    businessProfile: Object,
    financialOverview: Object,
    healthIndicators: Object,
    recentActivities: Array,
    quickStats: Object,
});

const { formatCurrency } = useFormatCurrency();
</script> 