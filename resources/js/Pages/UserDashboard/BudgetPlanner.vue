<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import IncomeDisplay from '@/Components/Shared/IncomeDisplay.vue';
import ExpensesDisplay from '@/Components/Shared/ExpensesDisplay.vue';
import NetIncomeDisplay from '@/Components/Shared/NetIncomeDisplay.vue';
import MonthlyChart from '@/Components/Shared/MonthlyChart.vue';

defineProps({
    income: {
        type: Array,
        required: true
    },
    expenses: {
        type: Array,
        required: true
    },
    goals: {
        type: Number,
        required: true
    },
    debts: {
        type: Number,
        required: true
    },
    netIncome: {
        type: Number,
        required: true
    },
    monthlyIncomes: {
        type: Object,
        required: true
    },
    monthlyExpenses: {
        type: Object,
        required: true
    }
});
</script>

<template>

    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <Sidebar>
                <div class="min-h-screen bg-gray-100 p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <IncomeDisplay :income="income" />
                        <ExpensesDisplay :expenses="expenses" :goals="goals" :debts="debts" />
                        <NetIncomeDisplay :netIncome="netIncome" />
                    </div>
                    <div class="mt-8">
                        <MonthlyChart :monthlyIncomes="monthlyIncomes" :monthlyExpenses="monthlyExpenses" />
                    </div>
                </div>
            </Sidebar>
        </div>
    </AuthenticatedLayout>
</template>
