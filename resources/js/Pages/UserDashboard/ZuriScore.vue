<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import Input from '@/Components/Shared/Input.vue';
import Button from '@/Components/Shared/Button.vue';
import Select from '@/Components/Shared/Select.vue';
import FileInput from '@/Components/Shared/FileInput.vue';
import Alert from '@/Components/Shared/Alert.vue';
import { useAlert } from '@/Components/Composables/useAlert';

const { openAlert, clearAlert, alertState } = useAlert()

const form = useForm({
    statement_type: '',
    statement_password: '',
    statement_file: null,
    statement_duration: '',
    email: '',
    email_confirmation: '',
    phone: '',
});

const handleFile = (file) => {
    form.statement_file = file;
};

function submitForm() {
    form.post(route('zuriscore.post'), {
        onSuccess: () => {
            form.reset();
            openAlert('success', "Report sent successfully!\nWe will get back to you soon!", 5000);
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors)
                .flat()
                .join(' ');
            openAlert('danger', errorMessages, 10000);
        }
    });
}
</script>

<template>

    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <Sidebar>
                <div class="min-h-screen bg-white p-6">
                    <h1 class="text-2xl font-semibold text-gray-900">Financial Statement Analysis</h1>
                    <Alert v-if="alertState" :type="alertState.type" :message="alertState.message"
                        :duration="alertState.duration" :auto-close="alertState.autoClose" @close="clearAlert" />
                    <form @submit.prevent="submitForm" class="space-y-4">
                        <Select v-model="form.statement_type" label="Statement type"
                            :options="[{ label: 'M-PESA', value: 'MPESA' }, { label: 'Equity Bank', value: 'Equity Bank' }]"
                            select_title="Statement type" />
                        <FileInput label="Upload Statement" v-model="form.statement_file" accept="application/pdf"
                            @file-selected="handleFile" />
                        <Input type="text" placeholder="Enter statement password" label="Statement Password (If Needed)"
                            v-model="form.statement_password" />
                        <Select label="Statement Period" select_title="Select duration" :options="[
                            { label: '1 Month - KES 10', value: 10 },
                            { label: '3 Month - KES 100', value: 100 },
                            { label: '6 Month - KES 200', value: 200 },
                            { label: '1 Year - KES 300', value: 300 },
                            { label: '2 Years - KES 500', value: 500 }
                        ]" v-model="form.statement_duration" />
                        <Input label="Email" placeholder="Email address to recieve report" v-model="form.email" />
                        <Input label="Confirm Email" placeholder="Confirm email address"
                            v-model="form.email_confirmation" />
                        <Input label="Pay with" placeholder="e.g., 0712345678 or +254712345678" v-model="form.phone"
                            :error="form.errors.phone" />
                        <p class="text-xs text-gray-500 -mt-2">Enter a valid Kenyan phone number (e.g., 0712345678)</p>

                        <Button type="submit" :processing="form.processing">
                            {{ form.processing ? 'Loading...' : 'Submit' }}
                        </Button>
                        <p v-show="form.processing" class="font-bold text-sm text-green-500">We are sending you an MPESA
                            STK Push to
                            {{ form.phone }}. Input your pin to continue</p>
                    </form>
                </div>
            </Sidebar>
        </div>
    </AuthenticatedLayout>
</template>
