<template>
    <Head title="Month-End Closing" />
    <MSMESidebar title="Month-End Closing">
        <div class="py-6">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Information Card -->
                <div
                    class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6"
                >
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg
                                class="h-6 w-6 text-blue-600"
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
                            <h3 class="text-lg font-medium text-blue-900">
                                What happens during month-end closing?
                            </h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>
                                        <strong>COGS Calculation:</strong>
                                        Opening Stock + Purchases - Closing
                                        Stock
                                    </li>
                                    <li>
                                        <strong>Depreciation:</strong> Automatic
                                        monthly depreciation for all fixed
                                        assets
                                    </li>
                                    <li>
                                        <strong>Balance Sheet Update:</strong>
                                        Assets, Liabilities, and Equity are
                                        recalculated
                                    </li>
                                    <li>
                                        <strong>Period Lock:</strong> The month
                                        is marked as closed and cannot be
                                        modified
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Form -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0">
                                <svg
                                    class="h-8 w-8 text-purple-600"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-2xl font-bold text-gray-900">
                                    Close Month
                                </h1>
                                <p class="text-gray-600 mt-1">
                                    Complete your monthly financial closing
                                    process
                                </p>
                            </div>
                        </div>

                        <form @submit.prevent="submit">
                            <div class="space-y-6">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Period End Date
                                    </label>
                                    <input
                                        type="date"
                                        v-model="form.period_end"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                        required
                                    />
                                    <p class="mt-1 text-sm text-gray-500">
                                        Select the last day of the month you
                                        want to close
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-2"
                                    >
                                        Closing Stock Value (KSh)
                                    </label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                                        >
                                            <span
                                                class="text-gray-500 sm:text-sm"
                                                >KSh</span
                                            >
                                        </div>
                                        <input
                                            type="number"
                                            step="0.01"
                                            v-model.number="
                                                form.closing_stock_value
                                            "
                                            class="w-full pl-12 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                            placeholder="0.00"
                                            required
                                        />
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Enter the total value of unsold
                                        inventory at month-end
                                    </p>
                                </div>

                                <!-- Summary Section -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h4
                                        class="text-sm font-medium text-gray-900 mb-3"
                                    >
                                        What will be calculated:
                                    </h4>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm"
                                    >
                                        <div>
                                            <span class="text-gray-600"
                                                >Opening Stock:</span
                                            >
                                            <span class="ml-2 font-medium"
                                                >From previous month's closing
                                                stock value</span
                                            >
                                        </div>
                                        <div>
                                            <span class="text-gray-600"
                                                >Purchases:</span
                                            >
                                            <span class="ml-2 font-medium"
                                                >This month's inventory
                                                purchases (category:
                                                inventory)</span
                                            >
                                        </div>
                                        <div>
                                            <span class="text-gray-600"
                                                >COGS:</span
                                            >
                                            <span class="ml-2 font-medium"
                                                >Opening + Purchases -
                                                Closing</span
                                            >
                                        </div>
                                        <div>
                                            <span class="text-gray-600"
                                                >Depreciation:</span
                                            >
                                            <span class="ml-2 font-medium"
                                                >Monthly depreciation for all
                                                assets</span
                                            >
                                        </div>
                                    </div>
                                </div>

                                <!-- How Opening Stock Works -->
                                <div
                                    class="bg-blue-50 border border-blue-200 rounded-lg p-4"
                                >
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <svg
                                                class="h-5 w-5 text-blue-600 mt-0.5"
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
                                            <h4
                                                class="text-sm font-medium text-blue-900 mb-2"
                                            >
                                                How Opening Stock Works:
                                            </h4>
                                            <ul
                                                class="text-sm text-blue-800 space-y-1"
                                            >
                                                <li>
                                                    •
                                                    <strong
                                                        >First month:</strong
                                                    >
                                                    Opening stock = 0 (no
                                                    previous month)
                                                </li>
                                                <li>
                                                    •
                                                    <strong
                                                        >Subsequent
                                                        months:</strong
                                                    >
                                                    Opening stock = Previous
                                                    month's closing stock value
                                                </li>
                                                <li>
                                                    •
                                                    <strong>Example:</strong> If
                                                    you closed January with KSh
                                                    50,000 stock, February's
                                                    opening stock = KSh 50,000
                                                </li>
                                                <li>
                                                    •
                                                    <strong
                                                        >Inventory
                                                        purchases:</strong
                                                    >
                                                    Only transactions with
                                                    category "inventory" are
                                                    counted as purchases
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <button
                                        :disabled="form.processing"
                                        type="submit"
                                        class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-colors disabled:opacity-50"
                                    >
                                        <svg
                                            v-if="form.processing"
                                            class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline"
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
                                                ? "Processing..."
                                                : "Close Period"
                                        }}
                                    </button>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <p>This action cannot be undone</p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </MSMESidebar>
    <Alert
        v-if="$page.props.flash?.success"
        type="success"
        :message="$page.props.flash.success"
    />
    <Alert
        v-if="$page.props.errors && Object.keys($page.props.errors).length"
        type="danger"
        :message="'Please fix the form errors'"
    />

    <!-- Month-End Summary (if available) -->
    <div
        v-if="$page.props.monthEndSummary"
        class="fixed bottom-4 right-4 bg-white p-4 rounded-lg shadow-lg border max-w-md"
    >
        <h4 class="font-semibold text-gray-900 mb-2">Month-End Summary</h4>
        <div class="text-sm space-y-1">
            <div>
                <strong>Opening Stock:</strong>
                {{ formatCurrency($page.props.monthEndSummary.opening_stock) }}
            </div>
            <div>
                <strong>Purchases:</strong>
                {{ formatCurrency($page.props.monthEndSummary.purchases) }}
            </div>
            <div>
                <strong>Closing Stock:</strong>
                {{ formatCurrency($page.props.monthEndSummary.closing_stock) }}
            </div>
            <div>
                <strong>COGS:</strong>
                {{ formatCurrency($page.props.monthEndSummary.cogs) }}
            </div>
            <div>
                <strong>Depreciation Posted:</strong>
                {{ formatCurrency($page.props.monthEndSummary.depreciation) }}
            </div>
        </div>
        <button
            @click="$page.props.monthEndSummary = null"
            class="mt-2 text-xs text-gray-500 hover:text-gray-700"
        >
            Close
        </button>
    </div>
</template>

<script setup>
import { Head, useForm, router } from "@inertiajs/vue3";
import MSMESidebar from "@/Components/MSMESidebar.vue";
import Alert from "@/Components/Shared/Alert.vue";
import { useFormatCurrency } from "@/Components/Composables/useFormatCurrency";

const { formatCurrency } = useFormatCurrency();

const form = useForm({
    period_end: new Date().toISOString().slice(0, 10),
    closing_stock_value: 0,
});

const submit = () => {
    // Validate form before submission
    if (!form.period_end) {
        alert("Please select a period end date");
        return;
    }

    if (!form.closing_stock_value || form.closing_stock_value < 0) {
        alert("Please enter a valid closing stock value");
        return;
    }

    form.post(route("month-end.close"), {
        onStart: () => {
            console.log("Starting month-end closing...");
        },
        onFinish: () => {
            console.log("Month-end closing finished");
        },
        onSuccess: (page) => {
            console.log("Month-end closing successful:", page);

            // Check if there's a success message
            if (page.props.flash?.success) {
                console.log("Success message:", page.props.flash.success);
                // The Alert component should automatically show this
            }

            // Reset the form for the next month
            form.reset();
            form.period_end = new Date().toISOString().slice(0, 10);
            form.closing_stock_value = 0;

            // Show additional feedback
            alert(
                "Month closed successfully! COGS calculated, depreciation posted, and balance sheet updated.",
            );
        },
        onError: (errors) => {
            console.error("Month-end closing failed:", errors);
            alert(
                "Failed to close month. Please check the console for details.",
            );
        },
    });
};
</script>
