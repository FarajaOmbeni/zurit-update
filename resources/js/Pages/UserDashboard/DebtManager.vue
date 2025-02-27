<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, defineProps } from 'vue';
import Sidebar from '@/Components/Sidebar.vue';
import DebtCard from '@/Components/Shared/DebtCard.vue';
import DebtsTable from '@/Components/Shared/DebtsTable.vue';

const props = defineProps({
    debts: Array,
});
const activeDebts = computed(() => props.debts.filter(debt => debt.status === 'active'));
const paidOffDebts = computed(() => props.debts.filter(debt => debt.status === 'paid_off'));
</script>

<template>

    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <Sidebar>
                <div class="container mx-auto p-4">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-purple-700">Your Debts</h1>
                        <!-- Additional filter or sort options can go here -->
                    </div>

                    <!-- Active Debts Section -->
                    <section class="mb-8">
                        <h2 class="text-xl font-semibold text-purple-700 mb-4">Active Debts</h2>
                        <div v-if="activeDebts.length" class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                            <DebtCard v-for="debt in activeDebts" :key="debt.debt_id" :debt="debt" />
                        </div>
                        <div v-else class="text-gray-600">
                            No active debts found.
                        </div>
                    </section>

                    <!-- Paid Off Debts Section -->
                    <section>
                        <h2 class="text-xl font-bold text-green-700 mb-4">Paid Off Debts</h2>
                        <div v-if="paidOffDebts.length" class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                            <DebtCard v-for="debt in paidOffDebts" :key="debt.debt_id" :debt="debt" />
                        </div>
                        <div v-else class="text-gray-600">
                            You have no paid off debts.
                        </div>
                    </section>
                </div>

                <div class="mt-4">
                    <h3 class="text-lg font-bold mb-4">Debt Full Details</h3>
                    <!-- Debt Table -->
                    <DebtsTable :debts="debts" />
                </div>
            </Sidebar>
        </div>
    </AuthenticatedLayout>
</template>
