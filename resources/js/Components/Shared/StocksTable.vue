<script setup>
import { computed, ref, watch } from 'vue';
import { formatCurrency } from '@/Components/Composables/useFormatCurrency';
import { formatDate } from '../Composables/useDateFormat';
import { useAlert } from '../Composables/useAlert';
import { useForm } from '@inertiajs/vue3';
const { openAlert, alertState } = useAlert();

const props = defineProps({
    investments: {
        type: Array,
        default: () => [],
    },
});
console.log(props.investments)

// Add emit for the edit action
const emit = defineEmits(['edit-investment']);

// Create a reactive copy of investments from props
const investmentList = ref([...props.investments]);

// (Optional) Watch for changes in props.investments and update the local reactive array
watch(() => props.investments, (newInvestments) => {
    investmentList.value = [...newInvestments];
});

// Filter investments that are active
const activeInvestments = computed(() => {
    return investmentList.value.filter(investment => investment.status === 'active');
});

// Function to handle edit button click
const handleEdit = (investment) => {
    emit('edit-investment', investment);
};

// Function to handle delete button click
const handleDelete = (investment) => {
    const msg = `Are you sure you want to delete "${investment.details_of_investment}"?`;
    if (window.confirm(msg)) {
        const form = useForm();
        form.delete(route('invest.destroy', investment.id), {
            onSuccess: () => {
                openAlert('success', 'Investment deleted successfully.');
                window.location.reload();
            },
            onError: () => {
                openAlert('danger', 'Failed to delete investment. Please try again.');
            }
        });
    }
};

const totalInitialAmount = computed(() =>
    activeInvestments.value.reduce(
        (total, investment) => total + Number(investment.initial_amount || 0),
        0,
    ),
);

// Fixed totalCumulativeAmount with proper number conversion
const totalCumulativeAmount = computed(() =>
    activeInvestments.value.reduce(
        (total, investment) => total + Number(investment.current_amount || 0),
        0,
    ),
);
</script>

<template>
    <Alert v-if="alertState" :type="alertState.type" :message="alertState.message" />
    <div class="overflow-x-auto mb-16 mt-4">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-purple-500 text-white">
                <tr>
                    <th class="px-4 py-2 text-left">Purchase Date</th>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Details</th>
                    <th class="px-4 py-2 text-right">Description</th>
                    <th class="px-4 py-2 text-right">Nbr. of Shares</th>
                    <th class="px-4 py-2 text-right">Price per Share</th>
                    <th class="px-4 py-2 text-right">Gross Revenue</th>
                    <th class="px-4 py-2 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <tr v-for="investment in activeInvestments" :key="investment.id" class="border-t">
                    <td class="px-4 py-2">{{ formatDate(investment.start_date) }}</td>
                    <td class="px-4 py-2">{{ investment.type }}</td>
                    <td class="px-4 py-2">{{ investment.details_of_investment }}</td>
                    <td class="px-4 py-2">{{ investment.description }}</td>
                    <td class="px-4 py-2 text-right">{{ investment.initial_amount }}</td>
                    <td class="px-4 py-2 text-right">KES {{ investment.expected_return_rate }}</td>
                    <td class="px-4 py-2 text-right">{{ formatCurrency(investment.current_amount) }}</td>
                    <td class="px-4 py-2 text-center">
                        <button @click="handleEdit(investment)"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded-md text-xs transition-colors duration-200">
                            Edit
                        </button>
                        <button @click="handleDelete(investment)"
                            class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded-md text-xs transition-colors duration-200">
                            Delete
                        </button>
                    </td>
                </tr>
            </tbody>
            <tfoot class="bg-gray-100 font-bold">
                <tr>
                    <td class="px-4 py-2">Totals</td>
                    <td class="px-4 py-2"></td>
                    <td class="px-4 py-2"></td>
                    <td class="px-4 py-2"></td>
                    <td class="px-4 py-2 text-right">{{ formatCurrency(totalInitialAmount) }}</td>
                    <td class="px-4 py-2"></td>
                    <td class="px-4 py-2 text-right">{{ formatCurrency(totalCumulativeAmount) }}</td>
                    <td class="px-4 py-2"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</template>