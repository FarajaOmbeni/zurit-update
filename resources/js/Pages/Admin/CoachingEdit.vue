<template>
    <AdminSidebar title="Edit Coach">

        <Head title="Edit Coach" />

        <div class="max-w-2xl mx-auto">
            <div class="mb-6">
                <Link :href="route('coaching.index')"
                    class="text-purple-600 hover:text-purple-700 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span>Back to Coaches</span>
                </Link>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Edit Coach</h2>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Name *
                        </label>
                        <input id="name" v-model="form.name" type="text"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.name }" required />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email *
                        </label>
                        <input id="email" v-model="form.email" type="email"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.email }" required />
                        <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone *
                        </label>
                        <input id="phone" v-model="form.phone" type="tel"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.phone }" required />
                        <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">
                            {{ form.errors.phone }}
                        </p>
                    </div>

                    <!-- Bio -->
                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                            Bio
                        </label>
                        <textarea id="bio" v-model="form.bio" rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.bio }"
                            placeholder="Tell us about the coach's background, expertise, and experience..."></textarea>
                        <p v-if="form.errors.bio" class="mt-1 text-sm text-red-600">
                            {{ form.errors.bio }}
                        </p>
                    </div>

                    <!-- Current Photo -->
                    <div v-if="coach.photo">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Current Photo
                        </label>
                        <div class="flex items-center space-x-4">
                            <img :src="coach.photo" :alt="coach.name" class="w-16 h-16 rounded-full object-cover" />
                            <div>
                                <p class="text-sm text-gray-600">Current photo</p>
                                <p class="text-xs text-gray-500">Upload a new photo to replace it</p>
                            </div>
                        </div>
                    </div>

                    <!-- Photo -->
                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ coach.photo ? 'New Photo' : 'Photo' }}
                        </label>
                        <input id="photo" @change="handlePhotoChange" type="file" accept="image/*"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.photo }" />
                        <p v-if="form.errors.photo" class="mt-1 text-sm text-red-600">
                            {{ form.errors.photo }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Accepted formats: JPEG, PNG, JPG, GIF (max 2MB)
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-3">
                        <Link :href="route('coaching.index')"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                        </Link>
                        <button type="submit" :disabled="form.processing"
                            class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 disabled:opacity-50">
                            <span v-if="form.processing">Updating...</span>
                            <span v-else>Update Coach</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Alert -->
        <Alert 
            v-if="alertState" 
            :type="alertState.type" 
            :message="alertState.message"
            :duration="alertState.duration" 
            :auto-close="alertState.autoClose" 
            @close="clearAlert" 
        />
    </AdminSidebar>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminSidebar from '@/Components/AdminSidebar.vue';
import Alert from '@/Components/Shared/Alert.vue';
import { useAlert } from '@/Components/Composables/useAlert';

const props = defineProps({
    coach: Object,
});

const { alertState, openAlert, clearAlert } = useAlert();
const form = useForm({
    name: props.coach.name,
    email: props.coach.email,
    phone: props.coach.phone,
    bio: props.coach.bio || '',
    photo: null,
    _method: 'PUT',
});

const handlePhotoChange = (event) => {
    form.photo = event.target.files[0];
};

const submit = () => {
    form.post(route('coaching.update', props.coach.id), {
        onSuccess: () => {
            openAlert('success', 'Coach updated successfully!', 5000);
        },
        onError: (errors) => {
            openAlert('danger', 'Error updating coach. Please check the form and try again.', 5000);
        }
    });
};
</script>