<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'
import DashboardBackButton from '@/Components/Shared/DashboardBackButton.vue'
import { reactive, ref } from 'vue'
import { formatCurrency } from '@/Components/Composables/useFormatCurrency'


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
/* -----------------------------------------------------------------------
   AMORTIZING-BOND STATE
------------------------------------------------------------------------ */
const amortizedBond = reactive({
    faceValue: '',
    couponRate: '',
    totalYears: '',
    amortStart: '',
    paymentsPerYear: '',
    marketRate: '',
    issueDate: '',
    valueDate: '',
    maturityDate: ''
})

const amortizedPrice = ref(null)   // numeric price
const amortizedSchedule = ref('')     // HTML table string

/* -----------------------------------------------------------------------
   MAIN CALCULATION
------------------------------------------------------------------------ */
const calculateAmortizedBond = () => {
    const face = +amortizedBond.faceValue
    const coupon = +amortizedBond.couponRate / 100
    const years = +amortizedBond.totalYears
    const start = +amortizedBond.amortStart
    const freq = +amortizedBond.paymentsPerYear
    const rate = +amortizedBond.marketRate / 100

    const totalPeriods = years * freq
    const startAmort = start * freq
    const periodCoupon = coupon / freq
    const periodRate = rate / freq
    const principalPay = totalPeriods > startAmort
        ? face / (totalPeriods - startAmort)
        : 0

    let remaining = face
    let price = 0
    let rows = ''

    for (let i = 1; i <= totalPeriods; i++) {
        const interest = remaining * periodCoupon
        const principal = i > startAmort ? principalPay : 0
        const total = interest + principal
        const discount = total / Math.pow(1 + periodRate, i)

        price += discount
        remaining -= principal

        rows += `
  <tr class="font-bold">
    <td class="border border-gray-300 px-2 py-1 text-right">${i}</td>
    <td class="border border-gray-300 px-2 py-1 text-right">${formatCurrency(interest)}</td>
    <td class="border border-gray-300 px-2 py-1 text-right">${formatCurrency(principal)}</td>
    <td class="border border-gray-300 px-2 py-1 text-right">${formatCurrency(total)}</td>
    <td class="border border-gray-300 px-2 py-1 text-right">${formatCurrency(discount)}</td>
    <td class="border border-gray-300 px-2 py-1 text-right">${formatCurrency(remaining)}</td>
  </tr>`

    }

    amortizedPrice.value = price
    amortizedSchedule.value = `
  <h4 class="text-lg font-semibold mt-4 mb-2 text-purple-700">
    Repayment Schedule
  </h4>
  <div class="overflow-x-auto">
    <table class="min-w-full border border-gray-300 text-sm">
      <thead>
        <tr class="bg-gray-100">
          <th class="px-2 py-1 text-right border border-gray-300">Period</th>
          <th class="px-2 py-1 text-right border border-gray-300">Interest</th>
          <th class="px-2 py-1 text-right border border-gray-300">Principal</th>
          <th class="px-2 py-1 text-right border border-gray-300">Total&nbsp;Pmt</th>
          <th class="px-2 py-1 text-right border border-gray-300">Discounted&nbsp;CF</th>
          <th class="px-2 py-1 text-right border border-gray-300">Remaining</th>
        </tr>
      </thead>
      <tbody>${rows}</tbody>
    </table>
  </div>`

}

</script>

<template>

    <Head title="Calculators" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <Sidebar>
                <DashboardBackButton />
                <div class="min-h-screen bg-white p-6 space-y-10">
                    <section class="max-w-2xl mx-auto space-y-6">
                        <h1 class="text-center text-4xl font-bold text-purple-600 mb-8">
                            Calculators
                        </h1>
                        <!-- Amortizing Bond -->
                        <details class="border rounded">
                            <summary class="cursor-pointer select-none p-4 bg-purple-700 text-white">
                                Armotizing Bond Calculator
                            </summary>

                            <div class="p-4 space-y-3">
                                <!-- Inputs ------------------------------------------------------------ -->
                                <label class="block text-sm font-semibold mb-1">
                                    Face Value (KES)
                                    <input type="number" v-model.number="amortizedBond.faceValue"
                                        class="w-full border p-2 rounded" />
                                </label>

                                <label class="block text-sm font-semibold mb-1">
                                    Coupon Rate (%)
                                    <input type="number" v-model.number="amortizedBond.couponRate"
                                        class="w-full border p-2 rounded" />
                                </label>

                                <label class="block text-sm font-semibold mb-1">
                                    Total Years
                                    <input type="number" v-model.number="amortizedBond.totalYears"
                                        class="w-full border p-2 rounded" />
                                </label>

                                <label class="block text-sm font-semibold mb-1">
                                    Amortization Starts After (Years)
                                    <input type="number" v-model.number="amortizedBond.amortStart"
                                        class="w-full border p-2 rounded" />
                                </label>

                                <label class="block text-sm font-semibold mb-1">
                                    Payments per Year
                                    <input type="number" v-model.number="amortizedBond.paymentsPerYear"
                                        class="w-full border p-2 rounded" />
                                </label>

                                <label class="block text-sm font-semibold mb-1">
                                    Market Rate (%)
                                    <input type="number" v-model.number="amortizedBond.marketRate"
                                        class="w-full border p-2 rounded" />
                                </label>

                                <!-- (Optional) Dates -->
                                <label class="block text-sm font-semibold mb-1">
                                    Issue Date
                                    <input type="date" v-model="amortizedBond.issueDate"
                                        class="w-full border p-2 rounded" />
                                </label>

                                <label class="block text-sm font-semibold mb-1">
                                    Value Date
                                    <input type="date" v-model="amortizedBond.valueDate"
                                        class="w-full border p-2 rounded" />
                                </label>

                                <label class="block text-sm font-semibold mb-1">
                                    Maturity Date
                                    <input type="date" v-model="amortizedBond.maturityDate"
                                        class="w-full border p-2 rounded" />
                                </label>

                                <!-- Button -->
                                <button @click="calculateAmortizedBond"
                                    class="bg-yellow-400 px-4 py-2 font-bold rounded w-full text-purple-700">
                                    Calculate
                                </button>

                                <!-- Results -->
                                <p v-if="amortizedPrice !== null" class="font-semibold text-green-700">
                                    Estimated Bond Price: {{ formatCurrency(amortizedPrice) }}
                                </p>

                                <!-- Schedule -->
                                <div v-if="amortizedSchedule" v-html="amortizedSchedule"></div>
                            </div>
                        </details>

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
                                        <tr class="font-bold" v-for="row in debt.schedule" :key="row.period">
                                            <td class="border border-gray-300 px-2 py-1 text-right">{{ row.period }}
                                            </td>
                                            <td class="border border-gray-300 px-2 py-1 text-right">{{
                                                formatCurrency(row.principal) }}</td>
                                            <td class="border border-gray-300 px-2 py-1 text-right">{{
                                                formatCurrency(row.interest) }}</td>
                                            <td class="border border-gray-300 px-2 py-1 text-right">{{
                                                formatCurrency(row.balance) }}</td>
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
                                Money Market and Sacco Growth
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
                                <p v-if="mm.result !== null" class="font-semibold text-green-600">
                                    Total contributions: {{ formatCurrency((mm.initial || 0) + (mm.monthly || 0) *
                                    (mm.months || 0)) }}<br>
                                    Net profit: {{ formatCurrency((mm.result || 0) - ((mm.initial || 0) + (mm.monthly ||
                                    0) * (mm.months || 0))) }}<br>
                                    Gross revenue after {{ mm.months }} months: {{ formatCurrency(mm.result || 0) }}
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