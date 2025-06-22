<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { formatCurrency } from '@/Components/Composables/useFormatCurrency'
import { useAlert } from '@/Components/Composables/useAlert'
import { useForm } from '@inertiajs/vue3'
import { insurances } from '../Variables/investmentTypes'
import { formatDate } from '../Composables/useDateFormat'

const { openAlert, alertState } = useAlert()

/* ───────────────── props & emits ──────────────── */
const props = defineProps < {
    investments: any[]
} > ()

const emit = defineEmits(['edit-investment'])

/* ───────────────── reactive copy of props ─────── */
const investmentList = ref([...props.investments])

watch(
    () => props.investments,
    (newInvestments) => {
        investmentList.value = [...newInvestments]
    }
)

/* ───────────────── helpers ────────────────────── */
function providerName(value: string) {
    const p = insurances.find(i => i.value === value)
    return p ? p.name : value            // fallback to raw value
}

/* Only *active* insurance policies */
const activePolicies = computed(() =>
    investmentList.value.filter(i => i.status === 'active')
)

/* Totals */
const totalPremiumAmount = computed(() =>
    activePolicies.value.reduce((t, i) => t + Number(i.initial_amount || 0), 0)
)
const totalMaturityValue = computed(() =>
    activePolicies.value.reduce((t, i) => t + Number(i.current_amount || 0), 0)
)

/* ───────────────── actions ─────────────────────── */
function handleEdit(investment: any) {
    emit('edit-investment', investment)
}

function handleDelete(investment: any) {
    if (!window.confirm(`Are you sure you want to delete "${investment.details_of_investment}"?`)) return

    const form = useForm()
    form.delete(route('invest.destroy', investment.id), {
        onSuccess: () => {
            openAlert('success', 'Policy deleted successfully.')
            window.location.reload()
        },
        onError: () => {
            openAlert('danger', 'Failed to delete policy. Please try again.')
        }
    })
}
</script>

<template>
    <Alert v-if="alertState" :type="alertState.type" :message="alertState.message" />

    <div class="overflow-x-auto mb-16 mt-4">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-purple-500 text-white">
                <tr>
                    <th class="px-4 py-2 text-left">Provider</th>
                    <th class="px-4 py-2 text-left">Policy Type</th>
                    <th class="px-4 py-2 text-left">Premium Frequecy</th>
                    <th class="px-4 py-2 text-left">Amount</th>
                    <th class="px-4 py-2 text-right">Maturity Date</th>
                    <th class="px-4 py-2 text-right">Expected&nbsp;Return&nbsp;%</th>
                    <th class="px-4 py-2 text-right">Maturity&nbsp;Value</th>
                    <th class="px-4 py-2 text-center">Actions</th>
                </tr>
            </thead>

            <tbody class="text-gray-700">
                <tr v-for="policy in activePolicies" :key="policy.id" class="border-t">
                    <td class="px-4 py-2">{{ providerName(policy.type) }}</td>
                    <td class="px-4 py-2">{{ policy.details_of_investment }}</td>
                    <td class="px-4 py-2">
                        {{ policy.frequency_of_return }}
                    </td>
                    <td class="px-4 py-2">
                        {{ formatCurrency(policy.initial_amount) }}
                    </td>
                    <td class="px-4 py-2 text-right">{{ formatDate(policy.target_date) }}</td>
                    <td class="px-4 py-2 text-right">
                        {{ Number(policy.expected_return_rate).toFixed(2) }}%
                    </td>
                    <td class="px-4 py-2 text-right">
                        {{ formatCurrency(policy.current_amount) }}
                    </td>
                    <td class="px-4 py-2 text-center">
                        <button @click="handleEdit(policy)"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded-md text-xs transition-colors">Edit</button>
                        <button @click="handleDelete(policy)"
                            class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded-md text-xs transition-colors">Delete</button>
                    </td>
                </tr>
            </tbody>

            <tfoot class="bg-gray-100 font-bold">
                <tr>
                    <td class="px-4 py-2">Totals</td>
                    <td class="px-4 py-2"></td>
                    <td class="px-4 py-2"></td>
                    <td class="px-4 py-2"></td>
                    <td class="px-4 py-2 text-right"></td>
                    <td class="px-4 py-2 text-right"></td>
                    <td class="px-4 py-2 text-right">{{ formatCurrency(totalMaturityValue) }}</td>
                    <td class="px-4 py-2"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</template>
