<script setup>
import { ref } from 'vue';
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

const mpesa_form = useForm({
    amount: ''
})

function sendStkPush() {
    mpesa_form.post(route('stk.push'), {
        onSuccess: () => {
            mpesa_form.reset();
            openAlert('success', "Mpesa stk push sent successfully!", 5000);
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors)
                .flat()
                .join(' ');
            openAlert('danger', errorMessages, 10000);
        }
    })
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
                        <Select v-model="form.statement_type" label="Statement type" :options="['MPESA', 'Equity Bank']"
                            select_title="Statement type" />
                        <FileInput label="Upload Statement" v-model="form.statement_file" accept="application/pdf"
                            @file-selected="handleFile"/>
                        <Input type="text" placeholder="Enter statement password" label="Statement Password (If Needed)"
                            v-model="form.statement_password" />
                        <Select label="Statement Period"
                            :options="['1 month', '3 months', '6 months', '1 year', '2 years']"
                            v-model="form.statement_duration" select_title="Select duration" />
                        <Input label="Email" placeholder="Email address to recieve report" v-model="form.email" />
                        <Input label="Confirm Email" placeholder="Confirm email address"
                            v-model="form.email_confirmation" />
                        <Button type="submit"> {{ form.processing ? 'Loading...' : 'Submit' }} </Button>
                    </form>
                    <p>MPESA TEST</p>
                    <form @submit="sendStkPush">
                        <input type="text" v-model="mpesa_form.amount" :disabled="mpesa_form.processing"><br>
                        <button type="submit" :disabled="mpesa_form.processing" class="bg-purple-500 text-white p-2">Submit</button>
                    </form>
                </div>
            </Sidebar>
        </div>
    </AuthenticatedLayout>
</template>
