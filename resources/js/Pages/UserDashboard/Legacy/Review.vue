<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'
import DashboardBackButton from '@/Components/Shared/DashboardBackButton.vue'
import { BuildingOfficeIcon, UserGroupIcon, ShieldCheckIcon, DocumentCheckIcon, BanknotesIcon, CalendarIcon } from '@heroicons/vue/24/outline'
import { computed } from 'vue'

const props = defineProps({
    assets: { type: Array, default: () => [] },
    investments: { type: Array, default: () => [] },
    beneficiaries: { type: Array, default: () => [] },
    // Controller currently sends a single (first) fiduciary record; handle object or array gracefully
    fiduciaries: { type: [Object, Array, null], default: () => null },
    // IMPORTANT: controller sends 'insurances' (not insuranceInvestments)
    insurances: { type: Array, default: () => [] },
})

// ---------- helpers ----------
function formatCurrencyKES(value) {
    if (value == null || value === '') return '—'
    const n = Number(value) || 0
    return new Intl.NumberFormat('en-KE', {
        style: 'currency',
        currency: 'KES',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(n)
}
function formatDateNice(date) {
    if (!date) return 'Not specified'
    if (/^\d{4}-\d{2}-\d{2}$/.test(date)) {
        const [y, m, d] = date.split('-').map(Number)
        return new Date(y, m - 1, d).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
    }
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
}
function assetAllocations(asset) {
    const rel = asset.beneficiary_allocations || asset.beneficiaryAllocations || []
    return rel.map(a => ({
        id: a.id,
        beneficiary_name: a.beneficiary?.full_name,
        percentage: a.percentage,
    })).filter(a => a.beneficiary_name)
}
const totalAssetValue = computed(() => {
    const assetsValue = props.assets.reduce((sum, a) => sum + (parseFloat(a.value) || 0), 0);
    const investmentsValue = props.investments.reduce((sum, i) => sum + (parseFloat(i.value) || 0), 0);
    return assetsValue + investmentsValue;
})
const fiduciaryList = computed(() => {
    // normalize to array for rendering
    if (Array.isArray(props.fiduciaries)) return props.fiduciaries
    return props.fiduciaries ? [props.fiduciaries] : []
})

const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''

</script>

<template>

    <Head title="Review - Legacy & Estate Planning" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <Sidebar>
                <DashboardBackButton />

                <div class="max-w-6xl mx-auto p-6">
                    <!-- Header -->
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Step 5: Review & Generate</h1>
                        <p class="text-gray-600">Review your entries before generating your legacy pack.</p>
                    </div>

                    <!-- Progress (compact) -->
                    <div class="hidden md:flex items-center space-x-4 mb-8">
                        <Link :href="route('legacy.assets')" class="flex items-center space-x-2">
                        <div
                            class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            ✓</div>
                        <span class="text-green-600 font-medium">Assets</span>
                        </Link>
                        <div class="w-12 h-px bg-green-600"></div>
                        <Link :href="route('legacy.beneficiaries')" class="flex items-center space-x-2">
                        <div
                            class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            ✓</div>
                        <span class="text-green-600 font-medium">Beneficiaries</span>
                        </Link>
                        <div class="w-12 h-px bg-green-600"></div>
                        <Link :href="route('legacy.fiduciaries')" class="flex items-center space-x-2">
                        <div
                            class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            ✓</div>
                        <span class="text-green-600 font-medium">Fiduciaries</span>
                        </Link>
                        <div class="w-12 h-px bg-green-600"></div>
                        <Link :href="route('legacy.insurance')" class="flex items-center space-x-2">
                        <div
                            class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            ✓</div>
                        <span class="text-green-600 font-medium">Insurance</span>
                        </Link>
                        <div class="w-12 h-px bg-green-600"></div>
                        <div class="flex items-center space-x-2">
                            <div
                                class="bg-purple-500 w-8 h-8 text-white rounded-full flex items-center justify-center text-sm font-medium">
                                5</div>
                            <span class="text-purple-600 font-medium">Review</span>
                        </div>
                    </div>

                    <!-- Snapshot grid -->
                    <div class="grid gap-6 md:grid-cols-2">
                        <!-- Assets -->
                        <section class="bg-white rounded-lg shadow-sm border p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-2">
                                    <BuildingOfficeIcon class="w-6 h-6 text-blue-600" />
                                    <h2 class="text-lg font-semibold text-gray-900">Assets</h2>
                                </div>
                                <Link :href="route('legacy.assets')" class="text-sm text-purple-700 hover:underline">
                                Edit</Link>
                            </div>

                            <div class="text-sm text-gray-600 mb-3">
                                <div>Total Asset Value: <span class="font-semibold text-gray-900">{{
                                    formatCurrencyKES(totalAssetValue) }}</span></div>
                                <div>Assets Count: <span class="font-semibold text-gray-900">{{ assets.length }}</span>
                                </div>
                            </div>

                            <div v-if="assets.length" class="space-y-3 max-h-64 overflow-y-auto pr-1">
                                <div v-for="a in assets" :key="a.id" class="border rounded p-3">
                                    <div class="font-medium text-gray-900">{{ a.name }}</div>
                                    <div class="text-xs text-gray-500 mb-1">
                                        Value: {{ formatCurrencyKES(a.value) }} • Acquired: {{
                                            formatDateNice(a.acquisition_date) }}
                                    </div>
                                    <div class="text-xs text-gray-600" v-if="(assetAllocations(a)).length">
                                        <div class="font-semibold mb-1">Beneficiary Allocations</div>
                                        <div class="space-y-0.5">
                                            <div v-for="al in assetAllocations(a)" :key="al.id"
                                                class="flex justify-between">
                                                <span>{{ al.beneficiary_name }}</span>
                                                <span class="font-medium">{{ al.percentage }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-sm text-gray-500">No assets added.</div>
                        </section>

                        <!-- Investments -->
                        <section class="bg-white rounded-lg shadow-sm border p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-2">
                                    <BanknotesIcon class="w-6 h-6 text-green-600" />
                                    <h2 class="text-lg font-semibold text-gray-900">Investments</h2>
                                </div>
                                <Link :href="route('legacy.assets')" class="text-sm text-purple-700 hover:underline">
                                Edit</Link>
                            </div>

                            <div class="text-sm text-gray-600 mb-3">
                                <div>Total Investment Value: <span class="font-semibold text-gray-900">{{
                                    formatCurrencyKES(investments.reduce((sum, i) => sum + (parseFloat(i.value) ||
                                        0), 0)) }}</span></div>
                                <div>Investments Count: <span class="font-semibold text-gray-900">{{ investments.length
                                        }}</span>
                                </div>
                            </div>

                            <div v-if="investments.length" class="space-y-3 max-h-64 overflow-y-auto pr-1">
                                <div v-for="i in investments" :key="i.id" class="border rounded p-3">
                                    <div class="font-medium text-gray-900">{{ i.name }}</div>
                                    <div class="text-xs text-gray-500 mb-1">
                                        Value: {{ formatCurrencyKES(i.value) }} • Type: {{ i.type }} • Started: {{
                                            formatDateNice(i.start_date) }}
                                    </div>
                                    <div class="text-xs text-gray-500" v-if="i.expected_return_rate">
                                        Expected Return: {{ i.expected_return_rate }}%
                                    </div>
                                    <div class="text-xs text-gray-500" v-if="i.status">
                                        Status: {{ i.status }}
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-sm text-gray-500">No investments found.</div>
                        </section>

                        <!-- Beneficiaries -->
                        <section class="bg-white rounded-lg shadow-sm border p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-2">
                                    <UserGroupIcon class="w-6 h-6 text-emerald-600" />
                                    <h2 class="text-lg font-semibold text-gray-900">Beneficiaries</h2>
                                </div>
                                <Link :href="route('legacy.beneficiaries')"
                                    class="text-sm text-purple-700 hover:underline">Edit</Link>
                            </div>

                            <div class="text-sm text-gray-600 mb-3">
                                <div>Total Beneficiaries: <span class="font-semibold text-gray-900">{{
                                    beneficiaries.length }}</span></div>
                            </div>

                            <div v-if="beneficiaries.length" class="space-y-3 max-h-64 overflow-y-auto pr-1">
                                <div v-for="b in beneficiaries" :key="b.id" class="border rounded p-3">
                                    <div class="font-medium text-gray-900">{{ b.full_name }}</div>
                                    <div class="text-xs text-gray-500">
                                        <span v-if="b.relationship">Relationship: {{ b.relationship }} • </span>
                                        <span v-if="b.is_minor">Minor</span>
                                        <span v-else>Adult</span>
                                    </div>
                                    <div class="text-xs text-gray-500" v-if="b.email || b.phone_number">
                                        <span v-if="b.email">Email: {{ b.email }} • </span>
                                        <span v-if="b.phone_number">Phone: {{ b.phone_number }}</span>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-sm text-gray-500">No beneficiaries added.</div>
                        </section>

                        <!-- Fiduciaries -->
                        <section class="bg-white rounded-lg shadow-sm border p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-2">
                                    <ShieldCheckIcon class="w-6 h-6 text-orange-600" />
                                    <h2 class="text-lg font-semibold text-gray-900">Fiduciaries (Companies)</h2>
                                </div>
                                <Link :href="route('legacy.fiduciaries')"
                                    class="text-sm text-purple-700 hover:underline">Edit</Link>
                            </div>

                            <div class="text-sm text-gray-600 mb-3">
                                <div>Total Companies: <span class="font-semibold text-gray-900">{{ fiduciaryList.length
                                }}</span></div>
                            </div>

                            <div v-if="fiduciaryList.length" class="space-y-3 max-h-64 overflow-y-auto pr-1">
                                <div v-for="f in fiduciaryList" :key="f.id || f.institution_name"
                                    class="border rounded p-3">
                                    <div class="font-medium text-gray-900">{{ f.institution_name }}</div>
                                    <div class="text-xs text-gray-500">
                                        <span v-if="f.institution_type">Type: {{ f.institution_type }} • </span>
                                        <span v-if="f.contact_name">Contact: {{ f.contact_name }}</span>
                                    </div>
                                    <div class="text-xs text-gray-500" v-if="f.email || f.phone">
                                        <span v-if="f.email">Email: {{ f.email }} • </span>
                                        <span v-if="f.phone">Phone: {{ f.phone }}</span>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-sm text-gray-500">No fiduciary companies added.</div>
                        </section>

                        <!-- Insurances -->
                        <section class="bg-white rounded-lg shadow-sm border p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-2">
                                    <DocumentCheckIcon class="w-6 h-6 text-indigo-600" />
                                    <h2 class="text-lg font-semibold text-gray-900">Insurance & Pensions</h2>
                                </div>
                                <Link :href="route('legacy.insurance')" class="text-sm text-purple-700 hover:underline">
                                Edit</Link>
                            </div>

                            <div class="text-sm text-gray-600 mb-3">
                                <div>Total Policies: <span class="font-semibold text-gray-900">{{ insurances.length
                                }}</span></div>
                            </div>

                            <div v-if="insurances.length" class="space-y-3 max-h-64 overflow-y-auto pr-1">
                                <div v-for="p in insurances" :key="p.id" class="border rounded p-3">
                                    <div class="font-medium text-gray-900">
                                        {{ p.provider_name }}
                                        <span class="text-xs text-gray-500">({{ p.type }})</span>
                                    </div>
                                    <div class="text-xs text-gray-600 mt-1 space-y-1">
                                        <div v-if="p.policy_number"><span class="font-medium">Policy #:</span> {{
                                            p.policy_number }}</div>
                                        <div class="flex items-center space-x-4">
                                            <div class="flex items-center space-x-1" v-if="p.coverage_amount">
                                                <BanknotesIcon class="w-4 h-4" />
                                                <span>Coverage: {{ formatCurrencyKES(p.coverage_amount) }}</span>
                                            </div>
                                            <div class="flex items-center space-x-1" v-if="p.premium_amount">
                                                <BanknotesIcon class="w-4 h-4" />
                                                <span>Premium: {{ formatCurrencyKES(p.premium_amount) }}</span>
                                            </div>
                                            <div class="flex items-center space-x-1" v-if="p.renewal_date">
                                                <CalendarIcon class="w-4 h-4" />
                                                <span>Renews: {{ formatDateNice(p.renewal_date) }}</span>
                                            </div>
                                        </div>
                                        <div v-if="p.notes"><span class="font-medium">Notes:</span> {{ p.notes }}</div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-sm text-gray-500">No policies added.</div>
                        </section>
                    </div>

                    <div class="flex justify-center pt-8 border-t mt-8">
                        <form :action="route('legacy.generate')" method="POST" target="_blank">
                            <input type="hidden" name="_token" :value="csrf" />
                            <button type="submit"
                                class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-8 rounded-lg flex items-center transition-colors duration-200 text-lg">
                                <DocumentCheckIcon class="w-6 h-6 mr-3" />
                                Generate Legacy Pack
                            </button>
                        </form>
                    </div>
                </div>
            </Sidebar>
        </div>
    </AuthenticatedLayout>
</template>
