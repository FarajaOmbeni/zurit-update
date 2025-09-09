<template>
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$emit('close')"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form @submit.prevent="submitForm">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                {{ entry ? 'Edit Transaction' : 'Add Transaction' }}
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <!-- Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                <select v-model="form.type" required 
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    <option value="">Select Type</option>
                                    <option value="income">Income</option>
                                    <option value="expense">Expense</option>
                                </select>
                                <p v-if="form.errors.type" class="mt-1 text-sm text-red-600">{{ form.errors.type }}</p>
                            </div>

                            <!-- Category -->
                            <div v-if="form.type">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                <select v-model="form.category" required 
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    <option value="">Select Category</option>
                                    <option v-for="(label, value) in availableCategories" :key="value" :value="value">{{ label }}</option>
                                </select>
                                <p v-if="form.errors.category" class="mt-1 text-sm text-red-600">{{ form.errors.category }}</p>
                            </div>

                            <!-- Amount -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Amount (KES)</label>
                                <input v-model="form.amount" type="number" step="0.01" min="0.01" required 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                       placeholder="0.00">
                                <p v-if="form.errors.amount" class="mt-1 text-sm text-red-600">{{ form.errors.amount }}</p>
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                                <select v-model="form.payment_method" required 
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    <option value="">Select Payment Method</option>
                                    <option v-for="(label, value) in paymentMethods" :key="value" :value="value">{{ label }}</option>
                                </select>
                                <p v-if="form.errors.payment_method" class="mt-1 text-sm text-red-600">{{ form.errors.payment_method }}</p>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea v-model="form.description" rows="3" 
                                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                          placeholder="Optional description..."></textarea>
                                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                            </div>

                            <!-- Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                <input v-model="form.entry_date" type="date" required 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                <p v-if="form.errors.entry_date" class="mt-1 text-sm text-red-600">{{ form.errors.entry_date }}</p>
                            </div>

                            <!-- Advanced Fields (Collapsible) -->
                            <div>
                                <button @click="showAdvanced = !showAdvanced" type="button" 
                                        class="flex items-center text-sm text-purple-600 hover:text-purple-800">
                                    <svg :class="['w-4 h-4 mr-1 transition-transform', showAdvanced ? 'rotate-90' : '']" 
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                    Advanced Options
                                </button>
                            </div>

                            <div v-if="showAdvanced" class="space-y-4 pt-4 border-t border-gray-200">
                                <!-- Reference Number -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Reference Number</label>
                                    <input v-model="form.reference_number" type="text" 
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                           placeholder="Invoice #, Receipt #, etc.">
                                </div>

                                <!-- Customer/Supplier -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ form.type === 'income' ? 'Customer' : 'Supplier' }}
                                    </label>
                                    <input v-model="form.customer_supplier" type="text" 
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                           :placeholder="`${form.type === 'income' ? 'Customer' : 'Supplier'} name`">
                                </div>

                                <!-- VAT Amount -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">VAT Amount</label>
                                    <input v-model="form.vat_amount" type="number" step="0.01" min="0" 
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                           placeholder="0.00">
                                </div>

                                <!-- Tax Amount -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Other Tax Amount</label>
                                    <input v-model="form.tax_amount" type="number" step="0.01" min="0" 
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                           placeholder="0.00">
                                </div>

                                <!-- Is Recurring -->
                                <div>
                                    <label class="flex items-center">
                                        <input v-model="form.is_recurring" type="checkbox" 
                                               class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                        <span class="ml-2 text-sm text-gray-700">This is a recurring transaction</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" :disabled="form.processing"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ form.processing ? 'Saving...' : (entry ? 'Update' : 'Add') }} Transaction
                        </button>
                        <button @click="$emit('close')" type="button" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    show: Boolean,
    entry: Object,
    categories: Object,
    paymentMethods: Object,
});

const emit = defineEmits(['close', 'saved']);

const showAdvanced = ref(false);

const form = useForm({
    type: '',
    category: '',
    amount: '',
    payment_method: '',
    description: '',
    entry_date: new Date().toISOString().split('T')[0],
    reference_number: '',
    customer_supplier: '',
    vat_amount: 0,
    tax_amount: 0,
    is_recurring: false,
});

const availableCategories = computed(() => {
    if (!form.type) return {};
    return props.categories[form.type] || {};
});

// Watch for entry changes to populate form
watch(() => props.entry, (entry) => {
    if (entry) {
        form.type = entry.type;
        form.category = entry.category;
        form.amount = entry.amount;
        form.payment_method = entry.payment_method;
        form.description = entry.description || '';
        form.entry_date = entry.entry_date;
        form.reference_number = entry.reference_number || '';
        form.customer_supplier = entry.customer_supplier || '';
        form.vat_amount = entry.vat_amount || 0;
        form.tax_amount = entry.tax_amount || 0;
        form.is_recurring = entry.is_recurring || false;
    } else {
        form.reset();
        form.entry_date = new Date().toISOString().split('T')[0];
    }
}, { immediate: true });

// Reset category when type changes
watch(() => form.type, () => {
    form.category = '';
});

const submitForm = () => {
    if (props.entry) {
        form.put(route('cashflow.update', props.entry.id), {
            onSuccess: () => emit('saved'),
        });
    } else {
        form.post(route('cashflow.store'), {
            onSuccess: () => emit('saved'),
        });
    }
};
</script> 