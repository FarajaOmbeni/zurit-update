<template>
    <Head title="Balance Sheet Details" />
    <MSMESidebar title="Balance Sheet Details">
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
                                    Balance Sheet
                                </h1>
                                <p class="text-gray-600 mt-2">
                                    As of
                                    {{
                                        formatDate(
                                            balanceSheetRecord.as_of_date,
                                        )
                                    }}
                                </p>
                            </div>
                            <div class="mt-4 md:mt-0 flex space-x-3">
                                <Link
                                    :href="route('balance-sheet.index')"
                                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors"
                                >
                                    Back to Records
                                </Link>
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
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
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
                                        Total Assets
                                    </p>
                                    <p
                                        class="text-2xl font-semibold text-gray-900"
                                    >
                                        {{
                                            formatCurrency(
                                                balanceSheetRecord.total_assets,
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
                                        Total Liabilities
                                    </p>
                                    <p
                                        class="text-2xl font-semibold text-gray-900"
                                    >
                                        {{
                                            formatCurrency(
                                                balanceSheetRecord.total_liabilities,
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
                                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                                            ></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Total Equity
                                    </p>
                                    <p
                                        class="text-2xl font-semibold text-gray-900"
                                    >
                                        {{
                                            formatCurrency(
                                                balanceSheetRecord.total_equity,
                                            )
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Balance Sheet -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Assets -->
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <h3
                                class="text-lg font-semibold text-gray-900 mb-4"
                            >
                                Assets
                            </h3>
                            <div class="space-y-4">
                                <!-- Current Assets -->
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-2">
                                        Current Assets
                                    </h4>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600"
                                                >Cash and Equivalents</span
                                            >
                                            <span class="font-semibold">{{
                                                formatCurrency(
                                                    balanceSheetRecord.cash_and_equivalents,
                                                )
                                            }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600"
                                                >Accounts Receivable</span
                                            >
                                            <span class="font-semibold">{{
                                                formatCurrency(
                                                    balanceSheetRecord.accounts_receivable,
                                                )
                                            }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600"
                                                >Inventory</span
                                            >
                                            <span class="font-semibold">{{
                                                formatCurrency(
                                                    balanceSheetRecord.inventory,
                                                )
                                            }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600"
                                                >Prepaid Expenses</span
                                            >
                                            <span class="font-semibold">{{
                                                formatCurrency(
                                                    balanceSheetRecord.prepaid_expenses,
                                                )
                                            }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600"
                                                >Other Current Assets</span
                                            >
                                            <span class="font-semibold">{{
                                                formatCurrency(
                                                    balanceSheetRecord.other_current_assets,
                                                )
                                            }}</span>
                                        </div>
                                        <div
                                            class="flex justify-between border-t pt-2"
                                        >
                                            <span
                                                class="font-medium text-gray-900"
                                                >Total Current Assets</span
                                            >
                                            <span
                                                class="font-bold text-green-600"
                                                >{{
                                                    formatCurrency(
                                                        balanceSheetRecord.total_current_assets,
                                                    )
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                </div>

                                <!-- Non-Current Assets -->
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-2">
                                        Non-Current Assets
                                    </h4>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600"
                                                >Property, Plant &
                                                Equipment</span
                                            >
                                            <span class="font-semibold">{{
                                                formatCurrency(
                                                    balanceSheetRecord.property_plant_equipment,
                                                )
                                            }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600"
                                                >Accumulated Depreciation</span
                                            >
                                            <span class="font-semibold">{{
                                                formatCurrency(
                                                    balanceSheetRecord.accumulated_depreciation,
                                                )
                                            }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600"
                                                >Intangible Assets</span
                                            >
                                            <span class="font-semibold">{{
                                                formatCurrency(
                                                    balanceSheetRecord.intangible_assets,
                                                )
                                            }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600"
                                                >Investments</span
                                            >
                                            <span class="font-semibold">{{
                                                formatCurrency(
                                                    balanceSheetRecord.investments,
                                                )
                                            }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600"
                                                >Other Non-Current Assets</span
                                            >
                                            <span class="font-semibold">{{
                                                formatCurrency(
                                                    balanceSheetRecord.other_non_current_assets,
                                                )
                                            }}</span>
                                        </div>
                                        <div
                                            class="flex justify-between border-t pt-2"
                                        >
                                            <span
                                                class="font-medium text-gray-900"
                                                >Total Non-Current Assets</span
                                            >
                                            <span
                                                class="font-bold text-green-600"
                                                >{{
                                                    formatCurrency(
                                                        balanceSheetRecord.total_non_current_assets,
                                                    )
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="flex justify-between border-t-2 pt-2"
                                >
                                    <span
                                        class="font-bold text-lg text-gray-900"
                                        >Total Assets</span
                                    >
                                    <span
                                        class="font-bold text-lg text-green-600"
                                        >{{
                                            formatCurrency(
                                                balanceSheetRecord.total_assets,
                                            )
                                        }}</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Liabilities and Equity -->
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <h3
                                class="text-lg font-semibold text-gray-900 mb-4"
                            >
                                Liabilities & Equity
                            </h3>
                            <div class="space-y-4">
                                <!-- Current Liabilities -->
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-2">
                                        Current Liabilities
                                    </h4>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600"
                                                >Accounts Payable</span
                                            >
                                            <span class="font-semibold">{{
                                                formatCurrency(
                                                    balanceSheetRecord.accounts_payable,
                                                )
                                            }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600"
                                                >Short-term Debt</span
                                            >
                                            <span class="font-semibold">{{
                                                formatCurrency(
                                                    balanceSheetRecord.short_term_debt,
                                                )
                                            }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600"
                                                >Accrued Liabilities</span
                                            >
                                            <span class="font-semibold">{{
                                                formatCurrency(
                                                    balanceSheetRecord.accrued_liabilities,
                                                )
                                            }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600"
                                                >Taxes Payable</span
                                            >
                                            <span class="font-semibold">{{
                                                formatCurrency(
                                                    balanceSheetRecord.taxes_payable,
                                                )
                                            }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600"
                                                >Other Current Liabilities</span
                                            >
                                            <span class="font-semibold">{{
                                                formatCurrency(
                                                    balanceSheetRecord.other_current_liabilities,
                                                )
                                            }}</span>
                                        </div>
                                        <div
                                            class="flex justify-between border-t pt-2"
                                        >
                                            <span
                                                class="font-medium text-gray-900"
                                                >Total Current Liabilities</span
                                            >
                                            <span
                                                class="font-bold text-red-600"
                                                >{{
                                                    formatCurrency(
                                                        balanceSheetRecord.total_current_liabilities,
                                                    )
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                </div>

                                <!-- Non-Current Liabilities -->
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-2">
                                        Non-Current Liabilities
                                    </h4>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600"
                                                >Long-term Debt</span
                                            >
                                            <span class="font-semibold">{{
                                                formatCurrency(
                                                    balanceSheetRecord.long_term_debt,
                                                )
                                            }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600"
                                                >Deferred Tax Liabilities</span
                                            >
                                            <span class="font-semibold">{{
                                                formatCurrency(
                                                    balanceSheetRecord.deferred_tax_liabilities,
                                                )
                                            }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600"
                                                >Other Non-Current
                                                Liabilities</span
                                            >
                                            <span class="font-semibold">{{
                                                formatCurrency(
                                                    balanceSheetRecord.other_non_current_liabilities,
                                                )
                                            }}</span>
                                        </div>
                                        <div
                                            class="flex justify-between border-t pt-2"
                                        >
                                            <span
                                                class="font-medium text-gray-900"
                                                >Total Non-Current
                                                Liabilities</span
                                            >
                                            <span
                                                class="font-bold text-red-600"
                                                >{{
                                                    formatCurrency(
                                                        balanceSheetRecord.total_non_current_liabilities,
                                                    )
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                </div>

                                <!-- Equity -->
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-2">
                                        Equity
                                    </h4>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600"
                                                >Share Capital</span
                                            >
                                            <span class="font-semibold">{{
                                                formatCurrency(
                                                    balanceSheetRecord.share_capital,
                                                )
                                            }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600"
                                                >Retained Earnings</span
                                            >
                                            <span class="font-semibold">{{
                                                formatCurrency(
                                                    balanceSheetRecord.retained_earnings,
                                                )
                                            }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600"
                                                >Other Equity</span
                                            >
                                            <span class="font-semibold">{{
                                                formatCurrency(
                                                    balanceSheetRecord.other_equity,
                                                )
                                            }}</span>
                                        </div>
                                        <div
                                            class="flex justify-between border-t pt-2"
                                        >
                                            <span
                                                class="font-medium text-gray-900"
                                                >Total Equity</span
                                            >
                                            <span
                                                class="font-bold text-blue-600"
                                                >{{
                                                    formatCurrency(
                                                        balanceSheetRecord.total_equity,
                                                    )
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="flex justify-between border-t-2 pt-2"
                                >
                                    <span
                                        class="font-bold text-lg text-gray-900"
                                        >Total Liabilities & Equity</span
                                    >
                                    <span
                                        class="font-bold text-lg text-blue-600"
                                        >{{
                                            formatCurrency(
                                                balanceSheetRecord.total_liabilities +
                                                    balanceSheetRecord.total_equity,
                                            )
                                        }}</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Ratios -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                >
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Financial Ratios
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center">
                                <p class="text-sm text-gray-600">
                                    Current Ratio
                                </p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{
                                        balanceSheetRecord.current_ratio.toFixed(
                                            2,
                                        )
                                    }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{
                                        getCurrentRatioStatus(
                                            balanceSheetRecord.current_ratio,
                                        )
                                    }}
                                </p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-600">
                                    Debt to Equity Ratio
                                </p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{
                                        balanceSheetRecord.debt_to_equity_ratio.toFixed(
                                            2,
                                        )
                                    }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{
                                        getDebtToEquityStatus(
                                            balanceSheetRecord.debt_to_equity_ratio,
                                        )
                                    }}
                                </p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-600">
                                    Balance Status
                                </p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{
                                        balanceSheetRecord.is_balanced
                                            ? "Balanced"
                                            : "Unbalanced"
                                    }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{
                                        balanceSheetRecord.is_balanced
                                            ? "Assets = Liabilities + Equity"
                                            : "Check calculations"
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div
                    v-if="balanceSheetRecord.notes"
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Notes
                        </h3>
                        <p class="text-gray-700 whitespace-pre-wrap">
                            {{ balanceSheetRecord.notes }}
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
                            Edit Notes
                        </h3>
                        <form @submit.prevent="updateNotes" class="space-y-4">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                    >Notes</label
                                >
                                <textarea
                                    v-model="editForm.notes"
                                    rows="4"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                    placeholder="Add any notes about this balance sheet..."
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
    </MSMESidebar>
</template>

<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import MSMESidebar from "@/Components/MSMESidebar.vue";
import { useFormatCurrency } from "@/Components/Composables/useFormatCurrency";
import { formatDate } from "@/Components/Composables/useDateFormat";
import { ref } from "vue";

const props = defineProps({
    balanceSheetRecord: Object,
});

const { formatCurrency } = useFormatCurrency();

const showEditModal = ref(false);

const editForm = useForm({
    notes: props.balanceSheetRecord.notes || "",
});

const openEditModal = () => {
    showEditModal.value = true;
    editForm.notes = props.balanceSheetRecord.notes || "";
};

const closeEditModal = () => {
    showEditModal.value = false;
};

const updateNotes = () => {
    editForm.put(route("balance-sheet.update", props.balanceSheetRecord.id), {
        onSuccess: () => {
            closeEditModal();
        },
    });
};

const getCurrentRatioStatus = (ratio) => {
    if (ratio >= 2) return "Excellent";
    if (ratio >= 1.5) return "Good";
    if (ratio >= 1) return "Adequate";
    return "Poor";
};

const getDebtToEquityStatus = (ratio) => {
    if (ratio <= 0.5) return "Conservative";
    if (ratio <= 1) return "Moderate";
    if (ratio <= 2) return "Aggressive";
    return "High Risk";
};
</script>
