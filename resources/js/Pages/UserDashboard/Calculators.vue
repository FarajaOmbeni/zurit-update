<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import { reactive, ref, computed } from 'vue'
import { moneyMarketFunds } from '@/Components/Variables/investmentTypes';

// Investment type selector
const investmentType = ref('')

// Reactive form data (all fields for the different investment types)
const form = reactive({
    // Treasury Bills
    tbInitial: 0,
    tbDays: 91,
    tbRate: 0,
    // Government Bonds
    gbInitial: 0,
    gbYears: 0,
    gbRate: 0,
    // Infrastructure Bonds
    ibInitial: 0,
    ibYears: 0,
    ibRate: 0,
    // Sacco Investments
    saccoMonthly: 0,
    saccoMonths: 0,
    saccoRate: 0,
    // Money Market Funds
    mmfName: '',
    mmfInitial: 0,
    mmfMonthly: 0,
    mmfMonths: 0,
    mmfRate: 0
})

// Store projected revenue
const projectedRevenue = ref(0)

// Calculate projected revenue based on the selected investment type
const calculateRevenue = () => {
    let revenue = 0
    switch (investmentType.value) {
        case 'treasuryBills':
            revenue = form.tbInitial * (1 + (form.tbRate / 100)) * (form.tbDays / 365)
            break
        case 'governmentBonds':
            const gbTotalInvested = form.gbInitial * form.gbYears
            revenue = (form.gbInitial * (1 + (form.gbRate / 100)) * form.gbYears) - gbTotalInvested
            break
        case 'infrastructureBonds':
            const ibTotalInvested = form.ibInitial * form.ibYears
            revenue = (form.ibInitial * (1 + (form.ibRate / 100)) * form.ibYears) - ibTotalInvested
            break
        case 'saccoInvestments':
            revenue = form.saccoMonthly * (1 + (form.saccoRate / 100)) ** form.saccoMonths / 12
            break
        case 'moneyMarketFunds':
            revenue = form.mmfMonthly * (1 + (form.mmfRate / 100)) ** form.mmfMonths / 12
            break
        default:
            revenue = 0
    }
    projectedRevenue.value = revenue.toFixed(2)
}

const handleSubmit = () => {
    calculateRevenue()
}

// Format the revenue display as currency
const projectedRevenueDisplay = computed(() => {
    return projectedRevenue.value ? `KES ${Math.round(projectedRevenue.value).toLocaleString()}` : 'KES0.00'
})
</script>

<template>

    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <Sidebar>
                <div class="min-h-screen bg-white p-6">
                    <!-- Projected Revenue Display -->
                    <div class="text-center mb-8">
                        <h1 class="text-4xl font-bold text-green-600">
                            Projected Profit: {{ projectedRevenueDisplay }}
                        </h1>
                    </div>

                    <!-- Investment Form -->
                    <form @submit.prevent="handleSubmit"
                        class="max-w-lg mx-auto bg-purple-700 p-6 rounded-lg shadow-lg text-white">
                        <!-- Investment Type Selection -->
                        <div class="mb-4">
                            <label for="investmentType" class="block text-sm font-semibold mb-1">
                                Investment Type
                            </label>
                            <select id="investmentType" v-model="investmentType"
                                class="w-full p-2 rounded border border-yellow-400 text-purple-900">
                                <option value="">Select Investment</option>
                                <option value="treasuryBills">Treasury Bills</option>
                                <option value="governmentBonds">Government Bonds</option>
                                <option value="infrastructureBonds">Infrastructure Bonds</option>
                                <option value="saccoInvestments">Sacco Investments</option>
                                <option value="moneyMarketFunds">Money Market Funds</option>
                            </select>
                        </div>

                        <!-- Treasury Bills Fields -->
                        <div v-if="investmentType === 'treasuryBills'" class="mb-4">
                            <div class="mb-4">
                                <label for="tbInitial" class="block text-sm font-semibold mb-1">Initial
                                    Investment</label>
                                <input id="tbInitial" type="number" v-model.number="form.tbInitial"
                                    placeholder="Enter amount"
                                    class="w-full p-2 rounded border border-yellow-400 text-purple-900" />
                            </div>
                            <div class="mb-4">
                                <label for="tbDays" class="block text-sm font-semibold mb-1">Number of Days</label>
                                <select id="tbDays" v-model.number="form.tbDays"
                                    class="w-full p-2 rounded border border-yellow-400 text-purple-900">
                                    <option value="91">91</option>
                                    <option value="182">182</option>
                                    <option value="384">384</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="tbRate" class="block text-sm font-semibold mb-1">Rate of Return (%)</label>
                                <input id="tbRate" type="number" v-model.number="form.tbRate" placeholder="Enter rate"
                                    class="w-full p-2 rounded border border-yellow-400 text-purple-900" />
                            </div>
                        </div>

                        <!-- Government Bonds Fields -->
                        <div v-if="investmentType === 'governmentBonds'" class="mb-4">
                            <div class="mb-4">
                                <label for="gbInitial" class="block text-sm font-semibold mb-1">Initial
                                    Investment</label>
                                <input id="gbInitial" type="number" v-model.number="form.gbInitial"
                                    placeholder="Enter amount"
                                    class="w-full p-2 rounded border border-yellow-400 text-purple-900" />
                            </div>
                            <div class="mb-4">
                                <label for="gbYears" class="block text-sm font-semibold mb-1">Number of Years</label>
                                <input id="gbYears" type="number" v-model.number="form.gbYears"
                                    placeholder="Enter years"
                                    class="w-full p-2 rounded border border-yellow-400 text-purple-900" />
                            </div>
                            <div class="mb-4">
                                <label for="gbRate" class="block text-sm font-semibold mb-1">Rate of Return (%)</label>
                                <input id="gbRate" type="number" v-model.number="form.gbRate" placeholder="Enter rate"
                                    class="w-full p-2 rounded border border-yellow-400 text-purple-900" />
                            </div>
                        </div>

                        <!-- Infrastructure Bonds Fields -->
                        <div v-if="investmentType === 'infrastructureBonds'" class="mb-4">
                            <div class="mb-4">
                                <label for="ibInitial" class="block text-sm font-semibold mb-1">Initial
                                    Investment</label>
                                <input id="ibInitial" type="number" v-model.number="form.ibInitial"
                                    placeholder="Enter amount"
                                    class="w-full p-2 rounded border border-yellow-400 text-purple-900" />
                            </div>
                            <div class="mb-4">
                                <label for="ibYears" class="block text-sm font-semibold mb-1">Number of Years</label>
                                <input id="ibYears" type="number" v-model.number="form.ibYears"
                                    placeholder="Enter years"
                                    class="w-full p-2 rounded border border-yellow-400 text-purple-900" />
                            </div>
                            <div class="mb-4">
                                <label for="ibRate" class="block text-sm font-semibold mb-1">Rate of Return (%)</label>
                                <input id="ibRate" type="number" v-model.number="form.ibRate" placeholder="Enter rate"
                                    class="w-full p-2 rounded border border-yellow-400 text-purple-900" />
                            </div>
                        </div>

                        <!-- Sacco Investments Fields -->
                        <div v-if="investmentType === 'saccoInvestments'" class="mb-4">
                            <div class="mb-4">
                                <label for="saccoMonthly" class="block text-sm font-semibold mb-1">Monthly
                                    Contribution</label>
                                <input id="saccoMonthly" type="number" v-model.number="form.saccoMonthly"
                                    placeholder="Enter contribution"
                                    class="w-full p-2 rounded border border-yellow-400 text-purple-900" />
                            </div>
                            <div class="mb-4">
                                <label for="saccoMonths" class="block text-sm font-semibold mb-1">Number of
                                    Months</label>
                                <input id="saccoMonths" type="number" v-model.number="form.saccoMonths"
                                    placeholder="Enter months"
                                    class="w-full p-2 rounded border border-yellow-400 text-purple-900" />
                            </div>
                            <div class="mb-4">
                                <label for="saccoRate" class="block text-sm font-semibold mb-1">Rate of Return
                                    (%)</label>
                                <input id="saccoRate" type="number" v-model.number="form.saccoRate"
                                    placeholder="Enter rate"
                                    class="w-full p-2 rounded border border-yellow-400 text-purple-900" />
                            </div>
                        </div>

                        <!-- Money Market Funds Fields -->
                        <div v-if="investmentType === 'moneyMarketFunds'" class="mb-4">
                            <div class="mb-4">
                                <label for="mmfName" class="block text-sm font-semibold mb-1">MMF Name</label>
                                <select id="mmfName" v-model="form.mmfName"
                                    class="w-full p-2 rounded border border-yellow-400 text-purple-900">
                                    <option value="" class="hidden">Select MMF Name</option>
                                    <option v-for="mmf in moneyMarketFunds" :key="mmf.value" :value="mmf.value">
                                        {{ mmf.label }}
                                    </option>
                                </select>
                            </div>
                            <!-- <div class="mb-4">
                                <label for="mmfInitial" class="block text-sm font-semibold mb-1">Initial
                                    Investment</label>
                                <input id="mmfInitial" type="number" v-model.number="form.mmfInitial"
                                    placeholder="Enter amount"
                                    class="w-full p-2 rounded border border-yellow-400 text-purple-900" />
                            </div> -->
                            <div class="mb-4">
                                <label for="mmfMonthly" class="block text-sm font-semibold mb-1">Monthly
                                    Contribution</label>
                                <input id="mmfMonthly" type="number" v-model.number="form.mmfMonthly"
                                    placeholder="Enter contribution"
                                    class="w-full p-2 rounded border border-yellow-400 text-purple-900" />
                            </div>
                            <div class="mb-4">
                                <label for="mmfMonths" class="block text-sm font-semibold mb-1">Number of Months</label>
                                <input id="mmfMonths" type="number" v-model.number="form.mmfMonths"
                                    placeholder="Enter months"
                                    class="w-full p-2 rounded border border-yellow-400 text-purple-900" />
                            </div>
                            <div class="mb-4">
                                <label for="mmfRate" class="block text-sm font-semibold mb-1">Rate of Return (%)</label>
                                <input id="mmfRate" type="number" v-model.number="form.mmfRate"
                                    placeholder="Rate will update automatically"
                                    class="w-full p-2 rounded border border-yellow-400 text-purple-900 bg-gray-100" />
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full py-3 mt-4 bg-yellow-400 text-purple-700 font-bold rounded hover:bg-yellow-500 transition">
                            Calculate Projected Revenue
                        </button>
                    </form>
                </div>
            </Sidebar>
        </div>
    </AuthenticatedLayout>
</template>
