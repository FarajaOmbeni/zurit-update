<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'
import { reactive, ref, computed } from 'vue'
import { moneyMarketFunds } from '@/Components/Variables/investmentTypes'

/* ─────────────────────────────
   1) Existing “Projected Revenue” tool
   ───────────────────────────── */
const investmentType = ref('')
const form = reactive({
    // Treasury Bills
    tbInitial: 0, tbDays: 91, tbRate: 0,
    // Government Bonds
    gbInitial: 0, gbYears: 0, gbRate: 0,
    // Infrastructure Bonds
    ibInitial: 0, ibYears: 0, ibRate: 0,
    // Sacco Investments
    saccoMonthly: 0, saccoMonths: 0, saccoRate: 0,
    // Money‑Market Funds
    mmfName: '', mmfInitial: 0, mmfMonthly: 0, mmfMonths: 0, mmfRate: 0,
})
const projectedRevenue = ref(0)

const calculateRevenue = () => {
    let revenue = 0
    switch (investmentType.value) {
        case 'treasuryBills':
            revenue = form.tbInitial * (1 + form.tbRate / 100) * (form.tbDays / 365)
            break
        case 'governmentBonds': {
            const total = form.gbInitial * form.gbYears
            revenue = form.gbInitial * (1 + form.gbRate / 100) * form.gbYears - total
            break
        }
        case 'infrastructureBonds': {
            const total = form.ibInitial * form.ibYears
            revenue = form.ibInitial * (1 + form.ibRate / 100) * form.ibYears - total
            break
        }
        case 'saccoInvestments':
            revenue = form.saccoMonthly * (1 + form.saccoRate / 100) ** (form.saccoMonths / 12) - form.saccoMonthly * form.saccoMonths
            break
        case 'moneyMarketFunds':
            revenue = form.mmfMonthly * (1 + form.mmfRate / 100) ** (form.mmfMonths / 12) - form.mmfMonthly * form.mmfMonths
            break
        default: revenue = 0
    }
    projectedRevenue.value = revenue
}
const handleSubmit = () => calculateRevenue()
const projectedRevenueDisplay = computed(() =>
    projectedRevenue.value ? `KES ${Math.round(projectedRevenue.value).toLocaleString()}` : 'KES 0.00'
)

/* ─────────────────────────────
   2) NEW – Five stand‑alone calculators
   ───────────────────────────── */
const debt = reactive({ amount: 0, rate: 0, years: 0, result: null })
function calcDebt() {
    const r = debt.rate / 100 / 12
    const n = debt.years * 12
    debt.result = (debt.amount * r) / (1 - Math.pow(1 + r, -n))
}

const sinking = reactive({ fv: 0, rate: 0, years: 0, result: null })
function calcSinking() {
    const r = sinking.rate / 100 / 12
    const n = sinking.years * 12
    sinking.result = sinking.fv * r / (Math.pow(1 + r, n) - 1)
}

const mm = reactive({ initial: 0, monthly: 0, rate: 0, months: 0, result: null })
function calcMM() {
    const r = mm.rate / 100 / 12
    let total = mm.initial * Math.pow(1 + r, mm.months)
    for (let i = 1; i <= mm.months; i++) total += mm.monthly * Math.pow(1 + r, mm.months - i)
    mm.result = total
}

const bond = reactive({
    nominal: 0, coupon: 0, years: 0, market: 0, freq: 1, tax: 15, result: null
})
function calcBond() {
    const gCoupon = bond.nominal * (bond.coupon / 100) / bond.freq
    const coupon = gCoupon * (1 - bond.tax / 100)
    const n = bond.years * bond.freq
    const r = bond.market / 100 / bond.freq
    let pv = 0
    for (let i = 1; i <= n; i++) pv += coupon / Math.pow(1 + r, i)
    pv += bond.nominal / Math.pow(1 + r, n)
    bond.result = pv
}

const tbill = reactive({ nominal: 0, rate: 0, tenor: 91, tax: 15, result: null })
function calcTbill() {
    const gross = tbill.nominal * (tbill.rate / 100) * tbill.tenor / 365
    const net = gross * (1 - tbill.tax / 100)
    tbill.result = tbill.nominal - net
}
</script>

<template>

    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <Sidebar>
                <div class="min-h-screen bg-white p-6 space-y-10">
                    <!-- ────── Projected‑Profit tool (your original) ────── -->
                    <section>
                        <h1 class="text-center text-4xl font-bold text-green-600 mb-8">
                            Projected Profit: {{ projectedRevenueDisplay }}
                        </h1>
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
                                        <label for="tbDays" class="block text-sm font-semibold mb-1">Number of
                                            Days</label>
                                        <select id="tbDays" v-model.number="form.tbDays"
                                            class="w-full p-2 rounded border border-yellow-400 text-purple-900">
                                            <option value="91">91</option>
                                            <option value="182">182</option>
                                            <option value="384">384</option>
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label for="tbRate" class="block text-sm font-semibold mb-1">Rate of Return
                                            (%)</label>
                                        <input id="tbRate" type="number" v-model.number="form.tbRate"
                                            placeholder="Enter rate"
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
                                        <label for="gbYears" class="block text-sm font-semibold mb-1">Number of
                                            Years</label>
                                        <input id="gbYears" type="number" v-model.number="form.gbYears"
                                            placeholder="Enter years"
                                            class="w-full p-2 rounded border border-yellow-400 text-purple-900" />
                                    </div>
                                    <div class="mb-4">
                                        <label for="gbRate" class="block text-sm font-semibold mb-1">Rate of Return
                                            (%)</label>
                                        <input id="gbRate" type="number" v-model.number="form.gbRate"
                                            placeholder="Enter rate"
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
                                        <label for="ibYears" class="block text-sm font-semibold mb-1">Number of
                                            Years</label>
                                        <input id="ibYears" type="number" v-model.number="form.ibYears"
                                            placeholder="Enter years"
                                            class="w-full p-2 rounded border border-yellow-400 text-purple-900" />
                                    </div>
                                    <div class="mb-4">
                                        <label for="ibRate" class="block text-sm font-semibold mb-1">Rate of Return
                                            (%)</label>
                                        <input id="ibRate" type="number" v-model.number="form.ibRate"
                                            placeholder="Enter rate"
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
                                        <label for="mmfMonths" class="block text-sm font-semibold mb-1">Number of
                                            Months</label>
                                        <input id="mmfMonths" type="number" v-model.number="form.mmfMonths"
                                            placeholder="Enter months"
                                            class="w-full p-2 rounded border border-yellow-400 text-purple-900" />
                                    </div>
                                    <div class="mb-4">
                                        <label for="mmfRate" class="block text-sm font-semibold mb-1">Rate of Return
                                            (%)</label>
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
                    </section>

                    <!-- ────── NEW: Accordion with 5 calculators ────── -->
                    <section class="max-w-2xl mx-auto space-y-6">
                        <!-- Debt -->
                        <details class="border rounded">
                            <summary class="cursor-pointer select-none p-4 bg-purple-700 text-white">
                                Debt Repayment – Monthly Payment
                            </summary>
                            <div class="p-4 space-y-3">
                                <label>Loan Amount (KES):</label>
                                <input v-model.number="debt.amount" type="number" placeholder="Loan amount (KES)"
                                    class="w-full border p-2 rounded" />
                                <label for="">Interest Rate (% per year):</label>
                                <input v-model.number="debt.rate" type="number" placeholder="Interest rate % per year"
                                    class="w-full border p-2 rounded" />
                                <label for="">Loan Term (Years):</label>
                                <input v-model.number="debt.years" type="number" placeholder="Loan term (years)"
                                    class="w-full border p-2 rounded" />
                                <button @click="calcDebt" class="bg-yellow-400 px-4 py-2 font-bold rounded w-full">
                                    Calculate
                                </button>
                                <p v-if="debt.result !== null" class="font-semibold">
                                    Monthly Payment: KES {{ debt.result.toFixed(2) }}
                                </p>
                            </div>
                        </details>

                        <!-- Sinking Fund -->
                        <details class="border rounded">
                            <summary class="cursor-pointer select-none p-4 bg-purple-700 text-white">
                                Sinking Fund – Monthly Deposit
                            </summary>
                            <div class="p-4 space-y-3">
                                <label for="">Future Value Needed (KES):</label>
                                <input v-model.number="sinking.fv" type="number" placeholder="Future value needed (KES)"
                                    class="w-full border p-2 rounded" />
                                <label for="">Interest Rate (% per year):</label>
                                <input v-model.number="sinking.rate" type="number"
                                    placeholder="Interest rate % per year" class="w-full border p-2 rounded" />
                                <label for="">Years to Save:</label>
                                <input v-model.number="sinking.years" type="number" placeholder="Years to save"
                                    class="w-full border p-2 rounded" />
                                <button @click="calcSinking" class="bg-yellow-400 px-4 py-2 font-bold rounded w-full">
                                    Calculate
                                </button>
                                <p v-if="sinking.result !== null" class="font-semibold">
                                    Monthly Deposit: KES {{ sinking.result.toFixed(2) }}
                                </p>
                            </div>
                        </details>

                        <!-- Money Market -->
                        <details class="border rounded">
                            <summary class="cursor-pointer select-none p-4 bg-purple-700 text-white">
                                Money Market Growth
                            </summary>
                            <div class="p-4 space-y-3">
                                <label for="">Initial Investment (KES):</label>
                                <input v-model.number="mm.initial" type="number" placeholder="Initial investment (KES)"
                                    class="w-full border p-2 rounded" />
                                <label for="">Monthly Top‑Up (KES):</label>
                                <input v-model.number="mm.monthly" type="number" placeholder="Monthly top‑up (KES)"
                                    class="w-full border p-2 rounded" />
                                <label for="">Annual Interest Rate (%):</label>
                                <input v-model.number="mm.rate" type="number" placeholder="Annual interest rate %"
                                    class="w-full border p-2 rounded" />
                                <label for="">Duration (Months):</label>
                                <input v-model.number="mm.months" type="number" placeholder="Duration (months)"
                                    class="w-full border p-2 rounded" />
                                <button @click="calcMM" class="bg-yellow-400 px-4 py-2 font-bold rounded w-full">
                                    Calculate
                                </button>
                                <p v-if="mm.result !== null" class="font-semibold">
                                    Estimated Value: KES {{ mm.result.toFixed(2) }}
                                </p>
                            </div>
                        </details>

                        <!-- Bond Valuation -->
                        <details class="border rounded">
                            <summary class="cursor-pointer select-none p-4 bg-purple-700 text-white">
                                Bond Valuation
                            </summary>
                            <div class="p-4 space-y-3">
                                <label for="">Nominal Value (KES):</label>
                                <input v-model.number="bond.nominal" type="number" placeholder="Nominal value (KES)"
                                    class="w-full border p-2 rounded" />
                                <label for="">Coupon Rate (%):</label>
                                <input v-model.number="bond.coupon" type="number" placeholder="Coupon rate %"
                                    class="w-full border p-2 rounded" />
                                <label for="">Years to Maturity:</label>
                                <input v-model.number="bond.years" type="number" placeholder="Years to maturity"
                                    class="w-full border p-2 rounded" />
                                <label for="">Market Interest Rate (%):</label>
                                <input v-model.number="bond.market" type="number" placeholder="Market rate %"
                                    class="w-full border p-2 rounded" />
                                <label for="">Coupon Payment Frequency:</label>
                                <select v-model.number="bond.freq" class="w-full border p-2 rounded">
                                    <option :value="1">Annual</option>
                                    <option :value="2">Semi‑annual</option>
                                    <option :value="4">Quarterly</option>
                                </select>
                                <label for="">Tax Rate on Interest (%):</label>
                                <input v-model.number="bond.tax" type="number" placeholder="Tax on coupon %"
                                    class="w-full border p-2 rounded" />
                                <button @click="calcBond" class="bg-yellow-400 px-4 py-2 font-bold rounded w-full">
                                    Calculate
                                </button>
                                <p v-if="bond.result !== null" class="font-semibold">
                                    Bond Price: KES {{ bond.result.toFixed(2) }}
                                </p>
                            </div>
                        </details>

                        <!-- Treasury Bill -->
                        <details class="border rounded">
                            <summary class="cursor-pointer select-none p-4 bg-purple-700 text-white">
                                Treasury‑Bill Price
                            </summary>
                            <div class="p-4 space-y-3">
                                <label for="">Nominal Value (KES):</label>
                                <input v-model.number="tbill.nominal" type="number" placeholder="Nominal value (KES)"
                                    class="w-full border p-2 rounded" />
                                <label for="">Discount Rate (% per annum):</label>
                                <input v-model.number="tbill.rate" type="number" placeholder="Discount rate % p.a."
                                    class="w-full border p-2 rounded" />
                                <label for="">Select Tenor:</label>
                                <select v-model.number="tbill.tenor" class="w-full border p-2 rounded">
                                    <option :value="91">91 days</option>
                                    <option :value="182">182 days</option>
                                    <option :value="364">364 days</option>
                                </select>
                                <label for="">Tax Rate (%):</label>
                                <input v-model.number="tbill.tax" type="number" placeholder="Tax rate %"
                                    class="w-full border p-2 rounded" />
                                <button @click="calcTbill" class="bg-yellow-400 px-4 py-2 font-bold rounded w-full">
                                    Calculate
                                </button>
                                <p v-if="tbill.result !== null" class="font-semibold">
                                    Net Purchase Price: KES {{ tbill.result.toFixed(2) }}
                                </p>
                            </div>
                        </details>
                    </section>
                </div>
            </Sidebar>
        </div>
    </AuthenticatedLayout>
</template>
