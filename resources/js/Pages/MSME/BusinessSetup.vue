<template>
    <Head title="Business Setup" />
    <AuthenticatedLayout>
        <div class="py-6">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Business Profile Setup</h1>
                        <p class="text-gray-600">Complete your business information to get started with financial management</p>
                    </div>
                </div>

                <!-- Setup Form -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submitForm" class="p-6 space-y-6">
                        <!-- Business Information Section -->
                        <div>
                            <h2 class="text-xl font-semibold mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Business Information
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Business Name *</label>
                                    <input v-model="form.business_name" type="text" required 
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                           placeholder="Enter your business name">
                                    <p v-if="form.errors.business_name" class="mt-1 text-sm text-red-600">{{ form.errors.business_name }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Business Type *</label>
                                    <select v-model="form.business_type" required 
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                        <option value="">Select Business Type</option>
                                        <option value="manufacturing">Manufacturing</option>
                                        <option value="retail">Retail</option>
                                        <option value="service">Service</option>
                                        <option value="agriculture">Agriculture</option>
                                        <option value="technology">Technology</option>
                                        <option value="food_beverage">Food & Beverage</option>
                                        <option value="construction">Construction</option>
                                        <option value="transport">Transport & Logistics</option>
                                        <option value="healthcare">Healthcare</option>
                                        <option value="education">Education</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Industry Sector</label>
                                    <input v-model="form.industry_sector" type="text" 
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                           placeholder="e.g., Textile, IT Services, etc.">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Registration Number</label>
                                    <input v-model="form.registration_number" type="text" 
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                           placeholder="Business registration number">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tax PIN</label>
                                    <input v-model="form.tax_pin" type="text" 
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                           placeholder="KRA PIN">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Number of Employees</label>
                                    <input v-model="form.employees_count" type="number" min="0" 
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                           placeholder="0">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Annual Revenue (KES)</label>
                                    <input v-model="form.annual_revenue" type="number" min="0" step="0.01" 
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                           placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information Section -->
                        <div class="border-t border-gray-200 pt-6">
                            <h2 class="text-xl font-semibold mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Contact Information
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Business Address</label>
                                    <textarea v-model="form.business_address" rows="3" 
                                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                              placeholder="Enter your business address"></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Business Phone</label>
                                    <input v-model="form.business_phone" type="tel" 
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                           placeholder="+254 700 000 000">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Business Email</label>
                                    <input v-model="form.business_email" type="email" 
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                           placeholder="business@example.com">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                                    <input v-model="form.website" type="url" 
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                           placeholder="https://www.example.com">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Primary Market</label>
                                    <select v-model="form.primary_market" 
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                        <option value="">Select Primary Market</option>
                                        <option value="local">Local</option>
                                        <option value="regional">Regional</option>
                                        <option value="national">National</option>
                                        <option value="international">International</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Business Details Section -->
                        <div class="border-t border-gray-200 pt-6">
                            <h2 class="text-xl font-semibold mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Business Details
                            </h2>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Target Customers</label>
                                    <textarea v-model="form.target_customers" rows="3" 
                                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                              placeholder="Describe your target customers..."></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Main Products/Services</label>
                                    <textarea v-model="form.main_products_services" rows="3" 
                                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                              placeholder="Describe your main products or services..."></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Business Description</label>
                                    <textarea v-model="form.business_description" rows="4" 
                                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                              placeholder="Provide a brief description of your business..."></textarea>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Business Age (Years)</label>
                                        <input v-model="form.business_age_years" type="number" min="0" 
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                               placeholder="0">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Operational Status</label>
                                        <select v-model="form.operational_status" 
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                            <option value="active">Active</option>
                                            <option value="seasonal">Seasonal</option>
                                            <option value="dormant">Dormant</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex justify-end space-x-3">
                                <Link :href="route('msme.dashboard')" 
                                      class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                                    Skip for Now
                                </Link>
                                <button type="submit" :disabled="form.processing"
                                        class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-md transition-colors disabled:opacity-50 flex items-center">
                                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ form.processing ? 'Saving...' : 'Save Business Profile' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    businessProfile: Object,
});

const form = useForm({
    business_name: props.businessProfile?.business_name || '',
    business_type: props.businessProfile?.business_type || '',
    industry_sector: props.businessProfile?.industry_sector || '',
    registration_number: props.businessProfile?.registration_number || '',
    tax_pin: props.businessProfile?.tax_pin || '',
    business_address: props.businessProfile?.business_address || '',
    business_phone: props.businessProfile?.business_phone || '',
    business_email: props.businessProfile?.business_email || '',
    website: props.businessProfile?.website || '',
    employees_count: props.businessProfile?.employees_count || '',
    annual_revenue: props.businessProfile?.annual_revenue || '',
    business_age_years: props.businessProfile?.business_age_years || '',
    primary_market: props.businessProfile?.primary_market || '',
    target_customers: props.businessProfile?.target_customers || '',
    main_products_services: props.businessProfile?.main_products_services || '',
    business_description: props.businessProfile?.business_description || '',
    operational_status: props.businessProfile?.operational_status || 'active',
});

const submitForm = () => {
    form.post(route('msme.business-profile.update'), {
        onSuccess: () => {
            // Redirect to dashboard on success
            window.location.href = route('msme.dashboard');
        },
    });
};
</script> 