<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import DashboardBackButton from '@/Components/Shared/DashboardBackButton.vue';
import { ShieldCheckIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    fiduciaries: {
        type: Object,
        default: () => null
    }
});

// Navigation function for progress indicator
function navigateToStep(step) {
    const routes = {
        'assets': route('legacy.assets'),
        'beneficiaries': route('legacy.beneficiaries'),
        'fiduciaries': route('legacy.fiduciaries'),
        'insurance': route('legacy.insurance'),
        'review': route('legacy.review')
    };

    if (routes[step]) {
        window.location.href = routes[step];
    }
}
</script>

<template>

    <Head title="Fiduciaries - Legacy & Estate Planning" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <Sidebar>
                <DashboardBackButton />

                <div class="max-w-6xl mx-auto p-6">
                    <!-- Header -->
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">
                            Step 3: Fiduciaries
                        </h1>
                        <p class="text-gray-600">
                            This page is for appointing executors, trustees, and guardians for your estate.
                        </p>
                    </div>

                    <!-- Progress Indicator -->
                    <div class="mb-8">
                        <div class="flex items-center space-x-4">
                            <!-- Step 1: Assets (Completed) -->
                            <Link :href="route('legacy.assets')"
                                class="flex items-center space-x-2 cursor-pointer hover:opacity-75 transition-opacity">
                            <div
                                class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-medium hover:bg-green-700 transition-colors">
                                ✓</div>
                            <span
                                class="text-green-600 font-medium hover:text-green-700 transition-colors">Assets</span>
                            </Link>
                            <div class="w-12 h-px bg-green-600"></div>

                            <!-- Step 2: Beneficiaries (Completed) -->
                            <Link :href="route('legacy.beneficiaries')"
                                class="flex items-center space-x-2 cursor-pointer hover:opacity-75 transition-opacity">
                            <div
                                class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-medium hover:bg-green-700 transition-colors">
                                ✓</div>
                            <span
                                class="text-green-600 font-medium hover:text-green-700 transition-colors">Beneficiaries</span>
                            </Link>
                            <div class="w-12 h-px bg-green-600"></div>

                            <!-- Step 3: Fiduciaries (Current) -->
                            <Link :href="route('legacy.fiduciaries')"
                                class="flex items-center space-x-2 cursor-pointer">
                            <div
                                class="bg-purple-500 w-8 h-8 text-white rounded-full flex items-center justify-center text-sm font-medium">
                                3</div>
                            <span class="text-purple-600 font-medium">Fiduciaries</span>
                            </Link>
                            <div class="w-12 h-px bg-gray-300"></div>

                            <!-- Step 4: Insurance -->
                            <Link :href="route('legacy.insurance')"
                                class="flex items-center space-x-2 cursor-pointer hover:opacity-75 transition-opacity">
                            <div
                                class="w-8 h-8 bg-gray-300 text-gray-500 rounded-full flex items-center justify-center text-sm hover:bg-purple-400 hover:text-white transition-colors">
                                4</div>
                            <span class="text-gray-500 hover:text-purple-600 transition-colors">Insurance</span>
                            </Link>
                            <div class="w-12 h-px bg-gray-300"></div>

                            <!-- Step 5: Review -->
                            <Link :href="route('legacy.review')"
                                class="flex items-center space-x-2 cursor-pointer hover:opacity-75 transition-opacity">
                            <div
                                class="w-8 h-8 bg-gray-300 text-gray-500 rounded-full flex items-center justify-center text-sm hover:bg-purple-400 hover:text-white transition-colors">
                                5</div>
                            <span class="text-gray-500 hover:text-purple-600 transition-colors">Review</span>
                            </Link>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
                        <div class="text-center py-12">
                            <ShieldCheckIcon class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Fiduciaries Management</h3>
                            <p class="text-gray-600 mb-6">
                                This page is for appointing executors, trustees, and guardians for your estate.
                            </p>
                            <p class="text-sm text-gray-500">
                                Coming soon - You'll be able to designate who will manage and execute your estate plan.
                            </p>
                        </div>
                    </div>

                    <!-- Continue Button -->
                    <div class="flex justify-end pt-8 border-t">
                        <Link :href="route('legacy.insurance')"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors duration-200">
                        Continue to Insurance →
                        </Link>
                    </div>
                </div>
            </Sidebar>
        </div>
    </AuthenticatedLayout>
</template>
