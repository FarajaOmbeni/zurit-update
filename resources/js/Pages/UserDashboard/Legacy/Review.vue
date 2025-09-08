<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import DashboardBackButton from '@/Components/Shared/DashboardBackButton.vue';
import { DocumentCheckIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    assets: {
        type: Array,
        default: () => []
    },
    beneficiaries: {
        type: Array,
        default: () => []
    },
    fiduciaries: {
        type: Object,
        default: () => null
    },
    insuranceInvestments: {
        type: Array,
        default: () => []
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

    <Head title="Review - Legacy & Estate Planning" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <Sidebar>
                <DashboardBackButton />

                <div class="max-w-6xl mx-auto p-6">
                    <!-- Header -->
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">
                            Step 5: Review & Generate
                        </h1>
                        <p class="text-gray-600">
                            This page is for reviewing your complete estate plan and generating your legacy pack.
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

                            <!-- Step 3: Fiduciaries (Completed) -->
                            <Link :href="route('legacy.fiduciaries')"
                                class="flex items-center space-x-2 cursor-pointer hover:opacity-75 transition-opacity">
                            <div
                                class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-medium hover:bg-green-700 transition-colors">
                                ✓</div>
                            <span
                                class="text-green-600 font-medium hover:text-green-700 transition-colors">Fiduciaries</span>
                            </Link>
                            <div class="w-12 h-px bg-green-600"></div>

                            <!-- Step 4: Insurance (Completed) -->
                            <Link :href="route('legacy.insurance')"
                                class="flex items-center space-x-2 cursor-pointer hover:opacity-75 transition-opacity">
                            <div
                                class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-medium hover:bg-green-700 transition-colors">
                                ✓</div>
                            <span
                                class="text-green-600 font-medium hover:text-green-700 transition-colors">Insurance</span>
                            </Link>
                            <div class="w-12 h-px bg-green-600"></div>

                            <!-- Step 5: Review (Current) -->
                            <Link :href="route('legacy.review')" class="flex items-center space-x-2 cursor-pointer">
                            <div
                                class="bg-purple-500 w-8 h-8 text-white rounded-full flex items-center justify-center text-sm font-medium">
                                5</div>
                            <span class="text-purple-600 font-medium">Review</span>
                            </Link>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
                        <div class="text-center py-12">
                            <DocumentCheckIcon class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Estate Plan Review</h3>
                            <p class="text-gray-600 mb-6">
                                This page is for reviewing your complete estate plan and generating your legacy pack.
                            </p>
                            <p class="text-sm text-gray-500">
                                Coming soon - You'll be able to review all your estate planning information and generate
                                a comprehensive legacy pack with all your documents.
                            </p>
                        </div>
                    </div>

                    <!-- Generate Pack Button -->
                    <div class="flex justify-center pt-8 border-t">
                        <button
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-8 rounded-lg flex items-center transition-colors duration-200 text-lg">
                            <DocumentCheckIcon class="w-6 h-6 mr-3" />
                            Generate Legacy Pack
                        </button>
                    </div>
                </div>
            </Sidebar>
        </div>
    </AuthenticatedLayout>
</template>
