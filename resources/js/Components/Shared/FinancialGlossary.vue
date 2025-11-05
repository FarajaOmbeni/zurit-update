<template>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    Financial Terms Glossary
                </h3>
                <button
                    @click="showGlossary = !showGlossary"
                    class="text-sm text-purple-600 hover:text-purple-800"
                >
                    {{ showGlossary ? "Hide" : "Show" }} Terms
                </button>
            </div>

            <div
                v-if="showGlossary"
                class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm"
            >
                <div class="space-y-3">
                    <div>
                        <h4 class="font-medium text-gray-900">
                            COGS (Cost of Goods Sold)
                        </h4>
                        <p class="text-gray-600">
                            Direct costs of producing goods sold. Calculated as:
                            Opening Stock + Purchases - Closing Stock
                        </p>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900">
                            Accounts Receivable
                        </h4>
                        <p class="text-gray-600">
                            Money owed to your business by customers who bought
                            on credit
                        </p>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900">
                            Accounts Payable
                        </h4>
                        <p class="text-gray-600">
                            Money your business owes to suppliers for purchases
                            made on credit
                        </p>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900">Depreciation</h4>
                        <p class="text-gray-600">
                            Reduction in value of fixed assets over time (e.g.,
                            equipment, furniture)
                        </p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div>
                        <h4 class="font-medium text-gray-900">Gross Profit</h4>
                        <p class="text-gray-600">
                            Revenue minus Cost of Goods Sold
                        </p>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900">Net Profit</h4>
                        <p class="text-gray-600">
                            Total revenue minus all expenses (operating,
                            interest, taxes)
                        </p>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900">Assets</h4>
                        <p class="text-gray-600">
                            What your business owns (cash, inventory, equipment,
                            property)
                        </p>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900">Liabilities</h4>
                        <p class="text-gray-600">
                            What your business owes (loans, payables, taxes)
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";

const showGlossary = ref(false);
</script>
