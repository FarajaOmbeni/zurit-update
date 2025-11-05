<template>
    <Head title="Profit & Loss Statement Details" />
    <MSMESidebar title="Profit & Loss Statement Details">
        <div v-if="!profitLossRecord" class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h1 class="text-2xl font-bold text-gray-900">
                            Loading...
                        </h1>
                        <p class="text-gray-600 mt-2">
                            Please wait while we load the Profit & Loss
                            statement.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
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
                                    <h1
                                        class="text-3xl font-bold text-gray-900"
                                    >
                                        Profit & Loss Statement
                                    </h1>
                                    <p class="text-gray-600 mt-2">
                                        {{
                                            profitLossRecord?.period_start
                                                ? formatDate(
                                                      profitLossRecord.period_start,
                                                  )
                                                : "N/A"
                                        }}
                                        -
                                        {{
                                            profitLossRecord?.period_end
                                                ? formatDate(
                                                      profitLossRecord.period_end,
                                                  )
                                                : "N/A"
                                        }}
                                    </p>
                                </div>
                                <div class="mt-4 md:mt-0 flex space-x-3">
                                    <Link
                                        :href="route('profit-loss.index')"
                                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors"
                                    >
                                        Back to Records
                                    </Link>
                                    <button
                                        @click="downloadPDF"
                                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors"
                                    >
                                        Download PDF
                                    </button>
                                    <button
                                        @click="downloadExcel"
                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors"
                                    >
                                        Download Excel
                                    </button>
                                    <button
                                        @click="openEditModal"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
                                    >
                                        Edit Notes
                                    </button>
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
                                            Total Revenue
                                        </p>
                                        <p
                                            class="text-2xl font-semibold text-gray-900"
                                        >
                                            {{
                                                formatCurrency(
                                                    profitLossRecord.revenue,
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
                                            Gross Profit
                                        </p>
                                        <p
                                            class="text-2xl font-semibold text-gray-900"
                                        >
                                            {{
                                                formatCurrency(
                                                    profitLossRecord.gross_profit,
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
                                            Operating Expenses
                                        </p>
                                        <p
                                            class="text-2xl font-semibold text-gray-900"
                                        >
                                            {{
                                                formatCurrency(
                                                    profitLossRecord.operating_expenses,
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
                                            :class="[
                                                'w-8 h-8 rounded-full flex items-center justify-center',
                                                profitLossRecord.net_profit >= 0
                                                    ? 'bg-green-100'
                                                    : 'bg-red-100',
                                            ]"
                                        >
                                            <svg
                                                :class="[
                                                    'w-4 h-4',
                                                    profitLossRecord.net_profit >=
                                                    0
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
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"
                                                ></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            Net Profit
                                        </p>
                                        <p
                                            :class="[
                                                'text-2xl font-semibold',
                                                profitLossRecord.net_profit >= 0
                                                    ? 'text-green-600'
                                                    : 'text-red-600',
                                            ]"
                                        >
                                            {{
                                                formatCurrency(
                                                    profitLossRecord.net_profit,
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed P&L Statement -->
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                    >
                        <div class="p-6">
                            <h3
                                class="text-lg font-semibold text-gray-900 mb-4"
                            >
                                Detailed Profit & Loss Statement
                            </h3>
                            <div class="space-y-4">
                                <!-- Revenue Section -->
                                <div class="border-b pb-4">
                                    <h4 class="font-medium text-gray-900 mb-2">
                                        Revenue
                                    </h4>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600"
                                            >Total Revenue</span
                                        >
                                        <span class="font-semibold">{{
                                            formatCurrency(
                                                profitLossRecord.revenue,
                                            )
                                        }}</span>
                                    </div>
                                </div>

                                <!-- Cost of Goods Sold -->
                                <div class="border-b pb-4">
                                    <h4 class="font-medium text-gray-900 mb-2">
                                        Cost of Goods Sold
                                    </h4>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600"
                                            >Cost of Goods Sold</span
                                        >
                                        <span class="font-semibold">{{
                                            formatCurrency(
                                                profitLossRecord.cost_of_goods_sold,
                                            )
                                        }}</span>
                                    </div>
                                    <div class="flex justify-between mt-2">
                                        <span class="font-medium text-gray-900"
                                            >Gross Profit</span
                                        >
                                        <span
                                            class="font-semibold text-green-600"
                                            >{{
                                                formatCurrency(
                                                    profitLossRecord.gross_profit,
                                                )
                                            }}</span
                                        >
                                    </div>
                                </div>

                                <!-- Operating Expenses -->
                                <div class="border-b pb-4">
                                    <h4 class="font-medium text-gray-900 mb-2">
                                        Operating Expenses
                                    </h4>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600"
                                            >Operating Expenses</span
                                        >
                                        <span class="font-semibold">{{
                                            formatCurrency(
                                                profitLossRecord.operating_expenses,
                                            )
                                        }}</span>
                                    </div>
                                </div>

                                <!-- Other Income/Expenses -->
                                <div class="border-b pb-4">
                                    <h4 class="font-medium text-gray-900 mb-2">
                                        Other Income & Expenses
                                    </h4>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600"
                                            >Other Income</span
                                        >
                                        <span class="font-semibold">{{
                                            formatCurrency(
                                                profitLossRecord.other_income,
                                            )
                                        }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600"
                                            >Other Expenses</span
                                        >
                                        <span class="font-semibold">{{
                                            formatCurrency(
                                                profitLossRecord.other_expenses,
                                            )
                                        }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600"
                                            >Depreciation</span
                                        >
                                        <span class="font-semibold">{{
                                            formatCurrency(
                                                profitLossRecord.depreciation || 0,
                                            )
                                        }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600"
                                            >Interest Expense</span
                                        >
                                        <span class="font-semibold">{{
                                            formatCurrency(
                                                profitLossRecord.interest_expense,
                                            )
                                        }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600"
                                            >Tax Expense</span
                                        >
                                        <span class="font-semibold">{{
                                            formatCurrency(
                                                profitLossRecord.tax_expense,
                                            )
                                        }}</span>
                                    </div>
                                </div>

                                <!-- Net Profit -->
                                <div>
                                    <div class="flex justify-between">
                                        <span
                                            class="font-bold text-lg text-gray-900"
                                            >Net Profit</span
                                        >
                                        <span
                                            :class="[
                                                'font-bold text-lg',
                                                profitLossRecord.net_profit >= 0
                                                    ? 'text-green-600'
                                                    : 'text-red-600',
                                            ]"
                                        >
                                            {{
                                                formatCurrency(
                                                    profitLossRecord.net_profit,
                                                )
                                            }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profitability Ratios -->
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                    >
                        <div class="p-6">
                            <h3
                                class="text-lg font-semibold text-gray-900 mb-4"
                            >
                                Profitability Ratios
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="text-center">
                                    <p class="text-sm text-gray-600">
                                        Gross Profit Margin
                                    </p>
                                    <p
                                        class="text-2xl font-semibold text-gray-900"
                                    >
                                        {{
                                            profitLossRecord.gross_profit_margin.toFixed(
                                                1,
                                            )
                                        }}%
                                    </p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm text-gray-600">
                                        Operating Margin
                                    </p>
                                    <p
                                        class="text-2xl font-semibold text-gray-900"
                                    >
                                        {{
                                            profitLossRecord.operating_margin.toFixed(
                                                1,
                                            )
                                        }}%
                                    </p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm text-gray-600">
                                        Net Profit Margin
                                    </p>
                                    <p
                                        class="text-2xl font-semibold text-gray-900"
                                    >
                                        {{
                                            profitLossRecord.net_profit_margin.toFixed(
                                                1,
                                            )
                                        }}%
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div
                        v-if="profitLossRecord.notes"
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <h3
                                class="text-lg font-semibold text-gray-900 mb-4"
                            >
                                Notes
                            </h3>
                            <p class="text-gray-700 whitespace-pre-wrap">
                                {{ profitLossRecord.notes }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Notes Modal -->
            <div
                v-if="showEditModal"
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
                        @click="closeEditModal"
                    ></div>
                    <span
                        class="hidden sm:inline-block sm:align-middle sm:h-screen"
                        >&#8203;</span
                    >
                    <div
                        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                    >
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3
                                class="text-lg leading-6 font-medium text-gray-900 mb-4"
                            >
                                Edit Notes
                            </h3>
                            <form
                                @submit.prevent="updateNotes"
                                class="space-y-4"
                            >
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >Notes</label
                                    >
                                    <textarea
                                        v-model="editForm.notes"
                                        rows="4"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                        placeholder="Add any notes about this P&L statement..."
                                    ></textarea>
                                </div>
                            </form>
                        </div>
                        <div
                            class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"
                        >
                            <button
                                @click="updateNotes"
                                :disabled="editForm.processing"
                                type="button"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                            >
                                {{
                                    editForm.processing
                                        ? "Updating..."
                                        : "Update Notes"
                                }}
                            </button>
                            <button
                                @click="closeEditModal"
                                type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MSMESidebar>
</template>

<script setup>
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import MSMESidebar from "@/Components/MSMESidebar.vue";
import { useFormatCurrency } from "@/Components/Composables/useFormatCurrency";
import { formatDate } from "@/Components/Composables/useDateFormat";
import { ref } from "vue";

const props = defineProps({
    profitLossRecord: Object,
});

// Debug: Log the data to console
console.log("ProfitLossRecord data:", props.profitLossRecord);

const { formatCurrency } = useFormatCurrency();

const showEditModal = ref(false);

const editForm = useForm({
    notes: props.profitLossRecord.notes || "",
});

const openEditModal = () => {
    showEditModal.value = true;
    editForm.notes = props.profitLossRecord.notes || "";
};

const closeEditModal = () => {
    showEditModal.value = false;
};

const updateNotes = () => {
    editForm.put(route("profit-loss.update", props.profitLossRecord.id), {
        onSuccess: () => {
            closeEditModal();
        },
    });
};

const downloadPDF = () => {
    const url = `/msme/profit-loss/${props.profitLossRecord.id}/download/pdf`;
    window.open(url, "_blank");
};

const downloadExcel = () => {
    const url = `/msme/profit-loss/${props.profitLossRecord.id}/download/excel`;
    window.open(url, "_blank");
};
</script>
