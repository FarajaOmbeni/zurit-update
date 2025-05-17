<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'
import { reactive, ref } from 'vue'
import { moneyMarketFunds } from '@/Components/Variables/investmentTypes'
import { formatCurrency } from '@/Components/Composables/useFormatCurrency'

/* ─────────────────────────────
   1) Existing "Projected Revenue" tool
   ───────────────────────────── */
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

// Calculate functions for each investment type
const calculateTreasuryBills = () => {
    return form.tbInitial * (1 + form.tbRate / 100) * (form.tbDays / 365)
}

const calculateGovernmentBonds = () => {
    const total = form.gbInitial * form.gbYears
    return form.gbInitial * (1 + form.gbRate / 100) * form.gbYears - total
}

const calculateInfrastructureBonds = () => {
    const total = form.ibInitial * form.ibYears
    return form.ibInitial * (1 + form.ibRate / 100) * form.ibYears - total
}

const calculateSaccoInvestments = () => {
    return form.saccoMonthly * (1 + form.saccoRate / 100) ** (form.saccoMonths / 12) - form.saccoMonthly * form.saccoMonths
}

const calculateMoneyMarketFunds = () => {
    return form.mmfMonthly * (1 + form.mmfRate / 100) ** (form.mmfMonths / 12) - form.mmfMonthly * form.mmfMonths
}

// Individual result values for each investment type
const tbResult = ref(null)
const gbResult = ref(null)
const ibResult = ref(null)
const saccoResult = ref(null)
const mmfResult = ref(null)

const calculateTB = () => {
    tbResult.value = calculateTreasuryBills()
    updateProjectedRevenue()
}

const calculateGB = () => {
    gbResult.value = calculateGovernmentBonds()
    updateProjectedRevenue()
}

const calculateIB = () => {
    ibResult.value = calculateInfrastructureBonds()
    updateProjectedRevenue()
}

const calculateSacco = () => {
    saccoResult.value = calculateSaccoInvestments()
    updateProjectedRevenue()
}

const calculateMMF = () => {
    mmfResult.value = calculateMoneyMarketFunds()
    updateProjectedRevenue()
}

/* ─────────────────────────────
   2) NEW – Five stand‑alone calculators
   ───────────────────────────── */
const debt = reactive({ amount: 0, rate: 0, years: 0, result: null, schedule: [] })
function calcDebt() {
    // clear previous run
    debt.schedule = [];

    // inputs
    const P = debt.amount;
    const r = debt.rate / 100 / 12;      // monthly rate
    const n = debt.years * 12;           // total payments

    if (!P || !r || !n) {
        debt.result = null;
        return;
    }

    // monthly payment
    const M = (P * r) / (1 - Math.pow(1 + r, -n));
    debt.result = M;

    // build amortisation rows
    let balance = P;
    for (let i = 1; i <= n; i++) {
        const interest = balance * r;
        const principal = M - interest;
        balance = Math.max(0, balance - principal);   // avoid −0

        debt.schedule.push({
            period: i,
            interest,
            principal,
            balance
        });
    }
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
    nominal: 0, coupon: 0, years: 0, market: 0, freq: 1, tax: 15, result: null, cashflows: []
})
function calcBond() {
    // clear previous run
    bond.cashflows = [];

    // 1. basic inputs
    const gross = bond.nominal * (bond.coupon / 100) / bond.freq;
    const coupon = gross * (1 - bond.tax / 100);     // after‑tax coupon
    const n = bond.years * bond.freq;            // total periods
    const r = bond.market / 100 / bond.freq;     // period discount rate

    // 2. present value of coupons
    let pv = 0;
    for (let i = 1; i <= n; i++) {
        const discounted = coupon / Math.pow(1 + r, i);
        pv += discounted;
        bond.cashflows.push({                // add a row
            period: i,
            cash: coupon,
            disc: discounted
        });
    }

    // 3. present value of face value
    const pvFace = bond.nominal / Math.pow(1 + r, n);
    pv += pvFace;
    bond.cashflows.push({
        period: n,
        cash: bond.nominal,                  // face value
        disc: pvFace,
        isFace: true
    });

    // 4. final price
    bond.result = pv;
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
                    <!-- ────── Projected‑Profit tool (converted to accordions) ────── -->
                    <section>
                        <div class="max-w-2xl mx-auto space-y-6">
                            <!-- Treasury Bills -->
                            <details class="border rounded">
                                <summary class="cursor-pointer select-none p-4 bg-purple-700 text-white">
                                    Treasury Bills
                                </summary>
                                <div class="p-4 space-y-3">
                                    <label for="tbInitial" class="block text-sm font-semibold mb-1">Initial Investment
                                        (KES)</label>
                                    <input id="tbInitial" type="number" v-model.number="form.tbInitial"
                                        placeholder="Enter amount" class="w-full border p-2 rounded" />

                                    <label for="tbDays" class="block text-sm font-semibold mb-1">Number of Days</label>
                                    <select id="tbDays" v-model.number="form.tbDays" class="w-full border p-2 rounded">
                                        <option value="91">91</option>
                                        <option value="182">182</option>
                                        <option value="384">384</option>
                                    </select>

                                    <label for="tbRate" class="block text-sm font-semibold mb-1">Rate of Return
                                        (%)</label>
                                    <input id="tbRate" type="number" v-model.number="form.tbRate"
                                        placeholder="Enter rate" class="w-full border p-2 rounded" />

                                    <button @click="calculateTB"
                                        class="bg-yellow-400 px-4 py-2 font-bold rounded w-full text-purple-700">
                                        Calculate
                                    </button>

                                    <p v-if="tbResult !== null" class="font-semibold text-green-700">
                                        Projected Revenue: {{ formatCurrency(tbResult) }}
                                    </p>
                                </div>
                            </details>

                            <!-- Government Bonds -->
                            <details class="border rounded">
                                <summary class="cursor-pointer select-none p-4 bg-purple-700 text-white">
                                    Government Bonds
                                </summary>
                                <div class="p-4 space-y-3">
                                    <label for="gbInitial" class="block text-sm font-semibold mb-1">Initial Investment
                                        (KES)</label>
                                    <input id="gbInitial" type="number" v-model.number="form.gbInitial"
                                        placeholder="Enter amount" class="w-full border p-2 rounded" />

                                    <label for="gbYears" class="block text-sm font-semibold mb-1">Number of
                                        Years</label>
                                    <input id="gbYears" type="number" v-model.number="form.gbYears"
                                        placeholder="Enter years" class="w-full border p-2 rounded" />

                                    <label for="gbRate" class="block text-sm font-semibold mb-1">Rate of Return
                                        (%)</label>
                                    <input id="gbRate" type="number" v-model.number="form.gbRate"
                                        placeholder="Enter rate" class="w-full border p-2 rounded" />

                                    <button @click="calculateGB"
                                        class="bg-yellow-400 px-4 py-2 font-bold rounded w-full text-purple-700">
                                        Calculate
                                    </button>

                                    <p v-if="gbResult !== null" class="font-semibold text-green-700">
                                        Projected Revenue: {{ formatCurrency(gbResult) }}
                                    </p>
                                </div>
                            </details>

                            <!-- Infrastructure Bonds -->
                            <details class="border rounded">
                                <summary class="cursor-pointer select-none p-4 bg-purple-700 text-white">
                                    Infrastructure Bonds
                                </summary>
                                <div class="p-4 space-y-3">
                                    <label for="ibInitial" class="block text-sm font-semibold mb-1">Initial Investment
                                        (KES)</label>
                                    <input id="ibInitial" type="number" v-model.number="form.ibInitial"
                                        placeholder="Enter amount" class="w-full border p-2 rounded" />

                                    <label for="ibYears" class="block text-sm font-semibold mb-1">Number of
                                        Years</label>
                                    <input id="ibYears" type="number" v-model.number="form.ibYears"
                                        placeholder="Enter years" class="w-full border p-2 rounded" />

                                    <label for="ibRate" class="block text-sm font-semibold mb-1">Rate of Return
                                        (%)</label>
                                    <input id="ibRate" type="number" v-model.number="form.ibRate"
                                        placeholder="Enter rate" class="w-full border p-2 rounded" />

                                    <button @click="calculateIB"
                                        class="bg-yellow-400 px-4 py-2 font-bold rounded w-full text-purple-700">
                                        Calculate
                                    </button>

                                    <p v-if="ibResult !== null" class="font-semibold text-green-700">
                                        Projected Revenue: {{ formatCurrency(ibResult) }}
                                    </p>
                                </div>
                            </details>

                            <!-- Sacco Investments -->
                            <details class="border rounded">
                                <summary class="cursor-pointer select-none p-4 bg-purple-700 text-white">
                                    Sacco Investments
                                </summary>
                                <div class="p-4 space-y-3">
                                    <label for="saccoMonthly" class="block text-sm font-semibold mb-1">Monthly
                                        Contribution (KES)</label>
                                    <input id="saccoMonthly" type="number" v-model.number="form.saccoMonthly"
                                        placeholder="Enter contribution" class="w-full border p-2 rounded" />

                                    <label for="saccoMonths" class="block text-sm font-semibold mb-1">Number of
                                        Months</label>
                                    <input id="saccoMonths" type="number" v-model.number="form.saccoMonths"
                                        placeholder="Enter months" class="w-full border p-2 rounded" />

                                    <label for="saccoRate" class="block text-sm font-semibold mb-1">Rate of Return
                                        (%)</label>
                                    <input id="saccoRate" type="number" v-model.number="form.saccoRate"
                                        placeholder="Enter rate" class="w-full border p-2 rounded" />

                                    <button @click="calculateSacco"
                                        class="bg-yellow-400 px-4 py-2 font-bold rounded w-full text-purple-700">
                                        Calculate
                                    </button>

                                    <p v-if="saccoResult !== null" class="font-semibold text-green-700">
                                        Projected Revenue: {{ formatCurrency(saccoResult) }}
                                    </p>
                                </div>
                            </details>

                            <!-- Money Market Funds -->
                            <details class="border rounded">
                                <summary class="cursor-pointer select-none p-4 bg-purple-700 text-white">
                                    Money Market Funds
                                </summary>
                                <div class="p-4 space-y-3">
                                    <label for="mmfName" class="block text-sm font-semibold mb-1">MMF Name</label>
                                    <select id="mmfName" v-model="form.mmfName" class="w-full border p-2 rounded">
                                        <option value="" class="hidden">Select MMF Name</option>
                                        <option v-for="mmf in moneyMarketFunds" :key="mmf.value" :value="mmf.value">
                                            {{ mmf.label }}
                                        </option>
                                    </select>

                                    <label for="mmfMonthly" class="block text-sm font-semibold mb-1">Monthly
                                        Contribution (KES)</label>
                                    <input id="mmfMonthly" type="number" v-model.number="form.mmfMonthly"
                                        placeholder="Enter contribution" class="w-full border p-2 rounded" />

                                    <label for="mmfMonths" class="block text-sm font-semibold mb-1">Number of
                                        Months</label>
                                    <input id="mmfMonths" type="number" v-model.number="form.mmfMonths"
                                        placeholder="Enter months" class="w-full border p-2 rounded" />

                                    <label for="mmfRate" class="block text-sm font-semibold mb-1">Rate of Return
                                        (%)</label>
                                    <input id="mmfRate" type="number" v-model.number="form.mmfRate"
                                        placeholder="Rate will update automatically"
                                        class="w-full border p-2 rounded bg-gray-100" />

                                    <button @click="calculateMMF"
                                        class="bg-yellow-400 px-4 py-2 font-bold rounded w-full text-purple-700">
                                        Calculate
                                    </button>

                                    <p v-if="mmfResult !== null" class="font-semibold text-green-700">
                                        Projected Revenue: {{ formatCurrency(mmfResult) }}
                                    </p>
                                </div>
                            </details>
                        </div>

                        <!-- Debt -->
                        <details class="border rounded">
                            <summary class="cursor-pointer select-none p-4 bg-purple-700 text-white">
                                Debt Repayment - Monthly Payment
                            </summary>
                            <div class="p-4 space-y-3">
                                <label class="block">Loan Amount (KES):</label>
                                <input v-model.number="debt.amount" type="number" placeholder="Loan amount (KES)"
                                    class="w-full border p-2 rounded" />
                                <label class="block" for="">Interest Rate (% per year):</label>
                                <input v-model.number="debt.rate" type="number" placeholder="Interest rate % per year"
                                    class="w-full border p-2 rounded" />
                                <label class="block" for="">Loan Term (Years):</label>
                                <input v-model.number="debt.years" type="number"
                                    placeholder="Loan term (years). If in months use decimals. e.g. 0.5 years = 6 months"
                                    class="w-full border p-2 rounded" />
                                <button @click="calcDebt" class="bg-yellow-400 px-4 py-2 font-bold rounded w-full">
                                    Calculate
                                </button>
                                <p v-if="debt.result !== null" class="font-semibold text-red-700">
                                    Monthly Payment: {{ formatCurrency(debt.result) }}
                                </p>
                                <table v-if="debt.schedule.length" class="mt-6 w-full text-sm border border-gray-200">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="p-2 text-left">Month</th>
                                            <th class="p-2 text-left">Payment</th>
                                            <th class="p-2 text-left">Interest</th>
                                            <th class="p-2 text-left">Remaining Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="row in debt.schedule" :key="row.period">
                                            <td class="p-2">{{ row.period }}</td>
                                            <td class="p-2">{{ formatCurrency(row.principal) }}</td>
                                            <td class="p-2">{{ formatCurrency(row.interest) }}</td>
                                            <td class="p-2">{{ formatCurrency(row.balance) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </details>

                        <!-- Sinking Fund -->
                        <details class="border rounded">
                            <summary class="cursor-pointer select-none p-4 bg-purple-700 text-white">
                                Sinking Fund - Monthly Deposit
                            </summary>
                            <div class="p-4 space-y-3">
                                <label class="block" for="">Future Value Needed (KES):</label>
                                <input v-model.number="sinking.fv" type="number" placeholder="Future value needed (KES)"
                                    class="w-full border p-2 rounded" />
                                <label class="block" for="">Interest Rate (% per year):</label>
                                <input v-model.number="sinking.rate" type="number"
                                    placeholder="Interest rate % per year" class="w-full border p-2 rounded" />
                                <label class="block" for="">Years to Save:</label>
                                <input v-model.number="sinking.years" type="number" placeholder="Years to save"
                                    class="w-full border p-2 rounded" />
                                <button @click="calcSinking" class="bg-yellow-400 px-4 py-2 font-bold rounded w-full">
                                    Calculate
                                </button>
                                <p v-if="sinking.result !== null" class="font-semibold">
                                    Monthly Deposit: {{ formatCurrency(sinking.result) }}
                                </p>
                            </div>
                        </details>

                        <!-- Money Market -->
                        <details class="border rounded">
                            <summary class="cursor-pointer select-none p-4 bg-purple-700 text-white">
                                Money Market Growth
                            </summary>
                            <div class="p-4 space-y-3">
                                <label class="block" for="">Initial Investment (KES):</label>
                                <input v-model.number="mm.initial" type="number" placeholder="Initial investment (KES)"
                                    class="w-full border p-2 rounded" />
                                <label class="block" for="">Monthly Top-Up (KES):</label>
                                <input v-model.number="mm.monthly" type="number" placeholder="Monthly top-up (KES)"
                                    class="w-full border p-2 rounded" />
                                <label class="block" for="">Annual Interest Rate (%):</label>
                                <input v-model.number="mm.rate" type="number" placeholder="Annual interest rate %"
                                    class="w-full border p-2 rounded" />
                                <label class="block" for="">Duration (Months):</label>
                                <input v-model.number="mm.months" type="number" placeholder="Duration (months)"
                                    class="w-full border p-2 rounded" />
                                <button @click="calcMM" class="bg-yellow-400 px-4 py-2 font-bold rounded w-full">
                                    Calculate
                                </button>
                                <p v-if="mm.result !== null" class="font-semibold">
                                    Estimated Value: {{ formatCurrency(mm.result) }}
                                </p>
                            </div>
                        </details>

                        <!-- Bond Valuation -->
                        <details class="border rounded">
                            <summary class="cursor-pointer select-none p-4 bg-purple-700 text-white">
                                Bond Valuation
                            </summary>
                            <div class="p-4 space-y-3">
                                <label class="block" for="">Nominal Value (KES):</label>
                                <input v-model.number="bond.nominal" type="number" placeholder="Nominal value (KES)"
                                    class="w-full border p-2 rounded" />
                                <label class="block" for="">Coupon Rate (%):</label>
                                <input v-model.number="bond.coupon" type="number" placeholder="Coupon rate %"
                                    class="w-full border p-2 rounded" />
                                <label class="block" for="">Years to Maturity:</label>
                                <input v-model.number="bond.years" type="number" placeholder="Years to maturity"
                                    class="w-full border p-2 rounded" />
                                <label class="block" for="">Market Interest Rate (%):</label>
                                <input v-model.number="bond.market" type="number" placeholder="Market rate %"
                                    class="w-full border p-2 rounded" />
                                <label class="block" for="">Coupon Payment Frequency:</label>
                                <select v-model.number="bond.freq" class="w-full border p-2 rounded">
                                    <option :value="1">Annual</option>
                                    <option :value="2">Semi annual</option>
                                    <option :value="4">Quarterly</option>
                                </select>
                                <label class="block" for="">Tax Rate on Interest (%):</label>
                                <input v-model.number="bond.tax" type="number" placeholder="Tax on coupon %"
                                    class="w-full border p-2 rounded" />
                                <button @click="calcBond" class="bg-yellow-400 px-4 py-2 font-bold rounded w-full">
                                    Calculate
                                </button>
                                <p v-if="bond.result !== null" class="font-semibold" id="resultBond">
                                    Bond Price: {{ formatCurrency(bond.result) }}
                                </p>
                                <table v-if="bond.cashflows.length" class="mt-6 w-full text-sm border border-gray-200">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="p-2 text-left">Period</th>
                                            <th class="p-2 text-left">Coupon</th>
                                            <th class="p-2 text-left">Discounted Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="row in bond.cashflows" :key="row.period"
                                            :class="row.isFace ? 'font-semibold' : ''">
                                            <td class="p-2">{{ row.period }}</td>
                                            <td class="p-2">
                                                {{ formatCurrency(row.cash) }}
                                                <span v-if="row.isFace">(Face Value)</span>
                                            </td>
                                            <td class="p-2">{{ formatCurrency(row.disc) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </details>

                        <!-- Treasury Bill -->
                        <details class="border rounded">
                            <summary class="cursor-pointer select-none p-4 bg-purple-700 text-white">
                                Treasury Bill Price
                            </summary>
                            <div class="p-4 space-y-3">
                                <label class="block" for="">Nominal Value (KES):</label>
                                <input v-model.number="tbill.nominal" type="number" placeholder="Nominal value (KES)"
                                    class="w-full border p-2 rounded" />
                                <label class="block" for="">Discount Rate (% per annum):</label>
                                <input v-model.number="tbill.rate" type="number" placeholder="Discount rate % p.a."
                                    class="w-full border p-2 rounded" />
                                <label class="block" for="">Select Tenor:</label>
                                <select v-model.number="tbill.tenor" class="w-full border p-2 rounded">
                                    <option :value="91">91 days</option>
                                    <option :value="182">182 days</option>
                                    <option :value="364">364 days</option>
                                </select>
                                <label class="block" for="">Tax Rate (%):</label>
                                <input v-model.number="tbill.tax" type="number" placeholder="Tax rate %"
                                    class="w-full border p-2 rounded" />
                                <button @click="calcTbill" class="bg-yellow-400 px-4 py-2 font-bold rounded w-full">
                                    Calculate
                                </button>
                                <p v-if="tbill.result !== null" class="font-semibold">
                                    Net Purchase Price: {{ formatCurrency(tbill.result) }}
                                </p>
                            </div>
                        </details>
                    </section>
                </div>
            </Sidebar>
        </div>
    </AuthenticatedLayout>
</template>